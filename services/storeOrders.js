const { db } = require('../database');
const { checkPayment } = require('./payment');
const { loadConfig } = require('../config');
const axios = require('axios');

async function sendTelegramNotification(message) {
  try {
    const cfg = await loadConfig();
    const token = cfg.telegram_bot_token;
    const chatId = cfg.telegram_chat_id;
    if (token && chatId) {
      await axios.post(`https://api.telegram.org/bot${token}/sendMessage`, {
        chat_id: chatId,
        text: message,
        parse_mode: 'Markdown'
      });
    }
  } catch (err) {
    console.error('Telegram notification error:', err.message);
  }
}

async function getOrderWithKey(orderId) {
  const { rows } = await db.execute(
    `SELECT o.*, k.key_value
     FROM store_orders o
     LEFT JOIN store_keys k ON k.id = o.key_id
     WHERE o.id = ?`,
    [orderId]
  );
  return rows[0] || null;
}

function orderExpired(order) {
  return order.expired_at && new Date(order.expired_at).getTime() < Date.now();
}

async function markExpired(order) {
  if (order.status !== 'expired') {
    await db.execute(`UPDATE store_orders SET status = 'expired' WHERE id = ?`, [order.id]);
  }
  return { success: true, status: 'expired', order: { ...order, status: 'expired' } };
}

async function claimAvailableKey(order) {
  for (let attempt = 0; attempt < 3; attempt++) {
    const { rows: keyRows } = await db.execute(
      `SELECT id, key_value
       FROM store_keys
       WHERE product_id = ? AND variant_id = ? AND is_used = 0
       ORDER BY id ASC
       LIMIT 1`,
      [order.product_id, order.variant_id]
    );

    if (!keyRows.length) return null;
    const key = keyRows[0];

    const result = await db.execute(
      `UPDATE store_keys
       SET is_used = 1, order_id = ?, used_at = datetime('now','localtime')
       WHERE id = ? AND is_used = 0`,
      [order.id, key.id]
    );

    if (result.rowsAffected === undefined || result.rowsAffected > 0) {
      return key;
    }
  }

  return null;
}

async function fulfillPaidOrder(order) {
  if (order.key_id && order.key_value) {
    return { success: true, status: 'paid', key: order.key_value, order };
  }

  const key = await claimAvailableKey(order);

  if (!key) {
    await db.execute(
      `UPDATE store_orders
       SET status = 'paid', paid_at = COALESCE(paid_at, datetime('now','localtime'))
       WHERE id = ?`,
      [order.id]
    );
    sendTelegramNotification(`⚠️ *ORDER BERHASIL TAPI STOK HABIS*\n\nOrder ID: \`${order.id}\`\nPembeli: ${order.customer_name} (${order.customer_email})\nJumlah: Rp ${order.amount}\n\nSegera hubungi pembeli dan tambahkan stok!`);
    return {
      success: true,
      status: 'paid_no_stock',
      message: 'Pembayaran diterima tapi stok habis. Hubungi admin.'
    };
  }

  await db.execute(
    `UPDATE store_orders
     SET status = 'paid', paid_at = COALESCE(paid_at, datetime('now','localtime')), key_id = ?
     WHERE id = ?`,
    [key.id, order.id]
  );
  
  sendTelegramNotification(`✅ *ORDER BERHASIL*\n\nOrder ID: \`${order.id}\`\nPembeli: ${order.customer_name}\nJumlah: Rp ${order.amount}\nKey yang diberikan: \`${key.key_value}\``);

  return { success: true, status: 'paid', key: key.key_value };
}

async function verifyAndFulfillOrder(orderId) {
  const order = await getOrderWithKey(orderId);
  if (!order) return { success: false, message: 'Order tidak ditemukan' };

  if (order.status === 'paid') {
    return fulfillPaidOrder(order);
  }

  if (order.status === 'expired') {
    return markExpired(order);
  }

  const paid = await checkPayment(order.unique_amount, order.created_at);
  if (paid) return fulfillPaidOrder(order);
  if (orderExpired(order)) return markExpired(order);
  return { success: true, status: 'pending' };
}

module.exports = {
  getOrderWithKey,
  verifyAndFulfillOrder
};
