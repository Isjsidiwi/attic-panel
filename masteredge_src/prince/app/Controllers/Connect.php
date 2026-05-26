<?php

namespace App\Controllers;

use App\Models\KeysModel;

class Connect extends BaseController
{
    protected $model, $game, $uKey, $sDev;

    public function __construct()
    {
        include('conn.php');
//=================================================
        $sql1 ="select * from onoff where id=1";
        $result1 = mysqli_query($conn, $sql1);
        $userDetails1 = mysqli_fetch_assoc($result1);
//=================================================
        $this->model = new KeysModel();
//=================================================
        if($userDetails1['status'] == 'on'){
        
        $this->maintenance = false;
        
        }
        if($userDetails1['status'] == 'off'){
        
        $this->maintenance = true;
        
        }
//=================================================
       $this->staticWords = "Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E";
    }

    public function index()
    {
        if ($this->request->getPost()) {
            return $this->index_post();
        } else {
            // Return splash screen HTML directly
            return "<!DOCTYPE html>
            <html>
            <head>
                <title>PRINCE VIP PANEL</title>
                <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
                <style>
                    :root {
                        --primary-color: #4f46e5;
                        --secondary-color: #7c3aed;
                        --accent-color: #06b6d4;
                        --dark-color: #0f172a;
                        --light-color: #ffffff;
                    }

                    body {
                        margin: 0;
                        padding: 0;
                        font-family: 'Poppins', sans-serif;
                        background-color: var(--dark-color);
                        color: var(--light-color);
                        overflow: hidden;
                    }

                    #particles-js {
                        position: fixed;
                        width: 100%;
                        height: 100%;
                        z-index: 1;
                    }

                    .splash-container {
                        position: relative;
                        z-index: 2;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        padding: 20px;
                    }

                    .glass-card {
                        background: rgba(255, 255, 255, 0.1);
                        border-radius: 20px;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
                        backdrop-filter: blur(10px);
                        padding: 40px;
                        text-align: center;
                        max-width: 700px;
                        width: 100%;
                        animation: fadeInScale 1s ease-out;
                    }

                    @keyframes fadeInScale {
                        from { opacity: 0; transform: scale(0.9); }
                        to { opacity: 1; transform: scale(1); }
                    }

                    .logo {
                        width: 150px;
                        height: 150px;
                        margin-bottom: 20px;
                        animation: pulse 2s infinite;
                    }

                    @keyframes pulse {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.05); }
                        100% { transform: scale(1); }
                    }

                    h1 {
                        font-size: 3.5em;
                        margin-bottom: 20px;
                        background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        animation: glow 1.5s infinite alternate;
                    }

                    @keyframes glow {
                        from { text-shadow: 0 0 5px rgba(79, 70, 229, 0.7); }
                        to { text-shadow: 0 0 20px rgba(79, 70, 229, 1); }
                    }

                    p {
                        font-size: 1.2em;
                        color: rgba(255, 255, 255, 0.8);
                        margin-bottom: 30px;
                        line-height: 1.6;
                    }

                    .contact-info {
                        margin-top: 30px;
                        padding: 20px;
                        background: rgba(255, 255, 255, 0.05);
                        border-radius: 15px;
                    }

                    .contact-info a {
                        color: var(--accent-color);
                        text-decoration: none;
                        font-weight: bold;
                        transition: all 0.3s ease;
                        padding: 8px 15px;
                        border-radius: 8px;
                        background: rgba(6, 182, 212, 0.1);
                    }

                    .contact-info a:hover {
                        color: var(--light-color);
                        background: var(--accent-color);
                        transform: translateY(-2px);
                    }

                    .loading-bar {
                        width: 200px;
                        height: 4px;
                        background: rgba(255, 255, 255, 0.1);
                        border-radius: 2px;
                        margin: 20px auto;
                        overflow: hidden;
                    }

                    .loading-progress {
                        width: 0%;
                        height: 100%;
                        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
                        animation: loading 3s ease-in-out forwards;
                    }

                    @keyframes loading {
                        0% { width: 0%; }
                        100% { width: 100%; }
                    }

                    @media (max-width: 768px) {
                        h1 { font-size: 2.5em; }
                        .glass-card { padding: 25px; }
                    }

                    @media (max-width: 480px) {
                        h1 { font-size: 1.8em; }
                        p { font-size: 1em; }
                        .glass-card { padding: 20px; }
                    }
                </style>
            </head>
            <body>
                <div id='particles-js'></div>
                <div class='splash-container'>
                    <div class='glass-card'>
                        <img src='https://i.postimg.cc/fT3B6LyM/photo-2025-05-18-09-25-07.jpg' alt='Logo' class='logo'>
                        <h1>Welcome to PRINCE VIP PANEL</h1>
                        <p>Your ultimate destination for powerful and reliable services. Experience seamless performance and cutting-edge features designed to elevate your experience.</p>
                        <div class='loading-bar'>
                            <div class='loading-progress'></div>
                        </div>
                        <div class='contact-info'>
                            <p>For inquiries or support, connect with us:</p>
                            <p>Telegram: <a href='https://t.me/aalyanmods' target='_blank'>@aalyanmods</a></p>
                        </div>
                    </div>
                </div>

                <script src='https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js'></script>
                <script>
                    particlesJS('particles-js', {
                        'particles': {
                            'number': {
                                'value': 80,
                                'density': {
                                    'enable': true,
                                    'value_area': 800
                                }
                            },
                            'color': {
                                'value': '#ffffff'
                            },
                            'shape': {
                                'type': 'circle'
                            },
                            'opacity': {
                                'value': 0.5,
                                'random': false
                            },
                            'size': {
                                'value': 3,
                                'random': true
                            },
                            'line_linked': {
                                'enable': true,
                                'distance': 150,
                                'color': '#ffffff',
                                'opacity': 0.4,
                                'width': 1
                            },
                            'move': {
                                'enable': true,
                                'speed': 6,
                                'direction': 'none',
                                'random': false,
                                'straight': false,
                                'out_mode': 'out',
                                'bounce': false
                            }
                        },
                        'interactivity': {
                            'detect_on': 'canvas',
                            'events': {
                                'onhover': {
                                    'enable': true,
                                    'mode': 'grab'
                                },
                                'onclick': {
                                    'enable': true,
                                    'mode': 'push'
                                },
                                'resize': true
                            },
                            'modes': {
                                'grab': {
                                    'distance': 140,
                                    'line_linked': {
                                        'opacity': 1
                                    }
                                },
                                'push': {
                                    'particles_nb': 4
                                }
                            }
                        },
                        'retina_detect': true
                    });
                </script>
            </body>
            </html>";
        }
    }

    public function index_post()
    {
        $isMT = $this->maintenance;
        $game = $this->request->getPost('game');
        $uKey = $this->request->getPost('user_key');
        $sDev = $this->request->getPost('serial');

        $form_rules = [
            'game' => 'required|alpha_dash',
            'user_key' => 'required|min_length[1]|max_length[36]',
            'serial' => 'required|alpha_dash'
        ];

        if (!$this->validate($form_rules)) {
            $data = [
                'status' => false,
                'reason' => "Bad Parameter",
            ];
            return $this->response->setJSON($data);
        }

        if ($isMT) {
            
            include('conn.php');
        
            $sql1 ="select * from onoff where id=1";
            $result1 = mysqli_query($conn, $sql1);
            $userDetails1 = mysqli_fetch_assoc($result1);
        
            
            $data = [
                'status' => true,
                'reason' => $userDetails1['myinput']
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
                $findKey = $model
                    ->getKeysGame(['user_key' => $uKey, 'game' => $game]);

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
                                    // ! false - devices max
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
    
                        if ($data['status']) {
                            
                            include('conn.php');
        
                            $sql2 ="select * from modname where id=1";
                            $result2 = mysqli_query($conn, $sql2);
                            $userDetails2 = mysqli_fetch_assoc($result2);
                            
                            $sql3 ="select * from _ftext where id=1";
                            $result3 = mysqli_query($conn, $sql3);
                            $userDetails3 = mysqli_fetch_assoc($result3);
                            
                            $sql4 = "SELECT expired_date FROM keys_code WHERE user_key='$uKey'";
                            $result4 = mysqli_query($conn, $sql4);
                            $userDetails4 = mysqli_fetch_assoc($result4);
//=================================================
        $sql = "SELECT * FROM Feature WHERE id=1";
        $result = mysqli_query($conn, $sql);
        $ModFeatureStatus = mysqli_fetch_assoc($result);
//=================================================
        $rngcnt = $time->getTimestamp();
//=================================================
                            $devicesAdd = checkDevicesAdd($sDev, $devices, $max_dev);
                            if ($devicesAdd) {
                                if (is_array($devicesAdd)) {
                                    $model->update($id_keys, $devicesAdd);
                                }
                                // ? game-user_key-serial-word di line 15
                                $real = "$game-$uKey-$sDev-$this->staticWords";
                                
                                $expiry = $findKey->expired_date;
                            if ($expiry == null) {
                                 $expiry = $time::now()->addDays($duration);
                            }
                            
                                $data = [
                                    'status' => true,
                                    'data' => [
                                        'real' => $real,
                                        'token' => md5($real),
                                        'modname' => $userDetails2['modname'],
                                        'mod_status' => $userDetails3['_status'],
                                        'credit' => $userDetails3['_ftext'],
                                        'ESP' => $ModFeatureStatus['ESP'],
                                        'Item' => $ModFeatureStatus['Item'],
                                        'AIM' => $ModFeatureStatus['AIM'],
                                        'SilentAim' => $ModFeatureStatus['SilentAim'],
                                        'BulletTrack' => $ModFeatureStatus['BulletTrack'],
                                        'Floating' => $ModFeatureStatus['Floating'],
                                        'Memory' => $ModFeatureStatus['Memory'],
                                        'Setting' => $ModFeatureStatus['Setting'],
                                        'expired_date' => $userDetails4['expired_date'],
                                        'EXP' => $expiry,
                                        'exdate' => $expiry,
                                        'device'=> $max_dev,
                                        'rng' => $rngcnt
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
