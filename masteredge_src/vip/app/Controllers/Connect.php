<?php

namespace App\Controllers;

use App\Models\KeysModel;
use App\Models\FuncationModel;

class Connect extends BaseController
{
    protected $model, $game, $uKey, $sDev;

    public function __construct()
    {
        $this->maintenance = false;
        $this->model = new KeysModel();
        $this->model1 = new FuncationModel();
        $this->staticWords = "Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E";
    }

public function loader()
{
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>AURA PANEL</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap");
            
            html, body {
                width: 100%;
                overflow-x: hidden;
                position: relative;
            }

            body {
                margin: 0;
                padding: 0;
                background: #0F0F0F;
                font-family: "Poppins", sans-serif;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .main-container {
                text-align: center;
                width: 100%;
                max-width: 100vw;
                padding: 20px;
                box-sizing: border-box;
            }

            .logo {
                width: clamp(100px, 30vw, 150px);
                height: clamp(100px, 30vw, 150px);
                margin-bottom: clamp(20px, 4vw, 30px);
                animation: float 3s ease-in-out infinite;
                object-fit: contain;
            }

            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
                100% { transform: translateY(0px); }
            }

            .title {
                font-size: clamp(24px, 5vw, 36px);
                font-weight: 800;
                color: #fff;
                text-transform: uppercase;
                margin-bottom: clamp(20px, 4vw, 30px);
                background: linear-gradient(45deg, #FF3366, #FF6B6B, #4ECDC4, #45B7D1);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: gradient 5s ease infinite;
                background-size: 300% 300%;
                width: 100%;
                max-width: 100%;
            }

            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .loader-wrapper {
                width: clamp(250px, 80vw, 300px);
                height: 10px;
                background: rgba(255,255,255,0.1);
                border-radius: 20px;
                overflow: hidden;
                position: relative;
                margin: 0 auto;
            }

            .loader-bar {
                position: absolute;
                width: 50%;
                height: 100%;
                background: linear-gradient(90deg, #FF3366, #FF6B6B);
                border-radius: 20px;
                animation: load 1.5s ease-in-out infinite;
            }

            @keyframes load {
                0% { left: -50%; }
                100% { left: 100%; }
            }

            .status {
                margin-top: clamp(15px, 3vw, 20px);
                color: #fff;
                font-size: clamp(14px, 4vw, 18px);
                opacity: 0.8;
                letter-spacing: 2px;
            }

            .welcome-screen {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: #0F0F0F;
                z-index: 1000;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .welcome-content {
                max-width: min(800px, 95%);
                margin: 0 auto;
                text-align: center;
                color: white;
                padding: clamp(10px, 3vw, 20px);
            }

            .welcome-title {
                font-size: clamp(32px, 6vw, 48px);
                margin-bottom: clamp(20px, 4vw, 30px);
                color: #FF3366;
                text-transform: uppercase;
                word-wrap: break-word;
            }

            .feature-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(min(250px, 100%), 1fr));
                gap: clamp(15px, 3vw, 20px);
                margin-top: clamp(30px, 5vw, 40px);
                padding: clamp(10px, 2vw, 15px);
                width: 100%;
            }

            .feature-card {
                background: rgba(255,255,255,0.05);
                border-radius: 15px;
                padding: clamp(15px, 3vw, 20px);
                transition: transform 0.3s ease;
                width: 100%;
            }

            .feature-card:hover {
                transform: translateY(-5px);
                background: rgba(255,255,255,0.1);
            }

            .feature-icon {
                font-size: clamp(30px, 6vw, 40px);
                margin-bottom: clamp(10px, 2vw, 15px);
            }

            .feature-title {
                font-size: clamp(16px, 4vw, 20px);
                font-weight: 600;
                margin-bottom: clamp(8px, 2vw, 10px);
                color: #FF6B6B;
            }

            .feature-desc {
                font-size: clamp(12px, 3vw, 14px);
                line-height: 1.6;
                color: rgba(255,255,255,0.7);
            }

            .btn-start {
                display: inline-block;
                margin-top: clamp(30px, 5vw, 40px);
                padding: clamp(10px, 3vw, 15px) clamp(20px, 5vw, 40px);
                background: linear-gradient(45deg, #FF3366, #FF6B6B);
                color: white;
                text-decoration: none;
                border-radius: 30px;
                font-weight: 600;
                font-size: clamp(14px, 3vw, 16px);
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                white-space: nowrap;
            }

            .btn-start:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(255, 51, 102, 0.3);
            }

            @media (max-width: 768px) {
                .welcome-content {
                    padding: 15px;
                    width: 100%;
                }
                
                .feature-grid {
                    grid-template-columns: 1fr;
                    width: 100%;
                }
                
                .btn-start {
                    width: 80%;
                    max-width: 300px;
                    text-align: center;
                }

                .feature-card {
                    margin: 10px 0;
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                body {
                    width: 100vw;
                }

                .welcome-title {
                    font-size: 28px;
                    padding: 0 10px;
                }

                .feature-icon {
                    font-size: 32px;
                }

                .feature-title {
                    font-size: 18px;
                }

                .feature-desc {
                    font-size: 12px;
                }
            }
        </style>
    </head>
    <body>
        <div class="main-container">
            <img src="https://vip-mod.xyz/public/NAMAN.png" alt="Logo" class="logo">
            <div class="title">PANEL ENGINE</div>
            <div class="loader-wrapper">
                <div class="loader-bar"></div>
            </div>
            <div class="status">Initializing System...</div>
        </div>

        <div class="welcome-screen">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome to Panel</h1>
                <p>Experience Gaming at a Whole New Level</p>
                
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🎮</div>
                        <div class="feature-title">Premium Mods</div>
                        <div class="feature-desc">Access exclusive gaming modifications with enhanced features.</div>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <div class="feature-title">Anti-Ban</div>
                        <div class="feature-desc">Advanced protection system to keep your account safe.</div>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">🚀</div>
                        <div class="feature-title">Performance</div>
                        <div class="feature-desc">Optimized for smooth gameplay without any lag.</div>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <div class="feature-title">Quick Updates</div>
                        <div class="feature-desc">Regular updates with new features and improvements.</div>
                    </div>
                </div>
                
                <a href="https://telegram.me/AALYANMODS" target="_blank" class="btn-start">Get Started</a>
            </div>
        </div>

        <script>
            const statusText = document.querySelector(".status");
            const loadingTexts = [
                "Initializing System...",
                "Checking Updates...",
                "Loading Resources...",
                "Almost Ready..."
            ];
            let currentText = 0;

            setInterval(() => {
                statusText.textContent = loadingTexts[currentText];
                currentText = (currentText + 1) % loadingTexts.length;
            }, 1000);

            setTimeout(() => {
                document.querySelector(".main-container").style.display = "none";
                document.querySelector(".welcome-screen").style.display = "block";
                
                document.querySelectorAll(".feature-card").forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = "0";
                        card.style.transform = "translateY(20px)";
                        card.style.transition = "all 0.5s ease";
                        
                        setTimeout(() => {
                            card.style.opacity = "1";
                            card.style.transform = "translateY(0)";
                        }, 100);
                    }, index * 200);
                });
            }, 5000);
        </script>
    </body>
    </html>';

    return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
}

    public function index()
    {
        if ($this->request->getPost()) {
            return $this->index_post();
        } else {
            return $this->loader();
        }
    }

    public function index_post()
    {
        $isMT = $this->maintenance;
        $game = $this->request->getPost('game');
        $uKey = $this->request->getPost('user_key');
        $sDev = $this->request->getPost('serial');

        $form_rules = [
            'game' => 'required|alpha_dash'
        ];

        if (!$this->validate($form_rules)) {
            $data = [
                'status' => false,
                'reason' => "Bad Parameter",
            ];
            return $this->response->setJSON($data);
        }

        if ($isMT) {
            $data = [
                'status' => false,
                'reason' => 'MAINTENANCE'
            ];
        } else {
            if (!$game or !$uKey or !$sDev) {
                $data = [
                    'status' => false,
                    'reason' => 'INVALID PARAMETER'
                ];
            } else {
                $time = new \CodeIgniter\I18n\Time;
                $model = $this->model;
                $model1 = $this->model1;
                $findKey = $model->getKeysGame(['user_key' => $uKey, 'game' => $game]);
                $findFuncation = $model1->getFuncation(['NAMAN_SINGH' => "NAMAN_SINGH", 'id_path' => 1]);
        
                if ($findKey) {
                    if ($findKey->status != 1) {
                        $data = [
                            'status' => false,
                            'reason' => 'USER BLOCKED'
                        ];
                    } else {
                        $id_keys = $findKey->id_keys;
                        $duration = $findKey->duration;
                        $expired = $findKey->expired_date;
                        $max_dev = $findKey->max_devices;
                        $devices = $findKey->devices;
    
                        function checkDevicesAdd($serial, $devices, $max_dev)
                        {
                            $lsDevice = explode(",", $devices);
                            $cDevices = isset($devices) ? count($lsDevice) : 0;
                            $serialOn = in_array($serial, $lsDevice);
    
                            if ($serialOn) {
                                return true;
                            } else {
                                if ($cDevices < $max_dev) {
                                    array_push($lsDevice, $serial);
                                    $setDevice = reduce_multiples(implode(",", $lsDevice), ",", true);
                                    return ['devices' => $setDevice];
                                } else {
                                    return false;
                                }
                            }
                        }
    
                        if (!$expired) {
                            $setExpired = $time::now()->addHours($duration);
                            $model->update($id_keys, ['expired_date' => $setExpired]);
                            $data['status'] = true;
                        } else {
                            if ($time::now()->isBefore($expired)) {
                                $data['status'] = true;
                            } else {
                                $data = [
                                    'status' => false,
                                    'reason' => 'EXPIRED KEY'
                                ];
                            }
                        }
    
                        if ($findFuncation->Online !== 'false') {
                            $data = [
                                'status' => false,
                                'reason' => $findFuncation->Maintenance
                            ];
                        } else {
                            if ($data['status']) {
                                $devicesAdd = checkDevicesAdd($sDev, $devices, $max_dev);
                                if ($devicesAdd) {
                                    if (is_array($devicesAdd)) {
                                        $model->update($id_keys, $devicesAdd);
                                    }
                                    $real = "$game-$uKey-$sDev-$this->staticWords";
                               
                                    if ($expired == null) {  
                                        $expiredX = $time::now()->addHours($duration);
                                    } else {  
                                        $expiredX = $findKey->expired_date;  
                                    }
                                 
                                    $data = [
                                        'status' => true,
                                        'data' => [
                                            'FuckYOU' => 1,
                                            'EXP' => $expiredX, 
                                            'token' => md5($real),
                                            'Online' => $findFuncation->Online,
                                            'Bullet' => $findFuncation->Bullet,
                                            'Aimbot' => $findFuncation->Aimbot,
                                            'Memory' => $findFuncation->Memory,
                                            'Esp' => $findFuncation->Esp,
                                            'ModName' => $findFuncation->ModName,
                                            'ftext' => $findFuncation->ftext,
                                            'status' => $findFuncation->status,
                                            'rng' => $time->getTimestamp()
                                        ],
                                    ];
                                } else {
                                    $data = [
                                        'status' => false,
                                        'reason' => 'MAX DEVICE REACHED'
                                    ];
                                }
                            }
                        }
                    }
                } else {
                    $data = [
                        'status' => false,
                        'reason' => 'USER OR GAME NOT REGISTERED'
                    ];
                }
            }
        }
        return $this->response->setJSON($data);
    }
}