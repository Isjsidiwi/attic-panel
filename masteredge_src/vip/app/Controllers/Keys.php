<?php
namespace App\Controllers;
use App\Models\HistoryModel;
use App\Models\KeysModel;
use App\Models\UserModel;
use Config\Services;
use App\Models\FuncationModel;
 
class Keys extends BaseController
{
    protected $userModel, $model, $user;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->user = $this->userModel->getUser();
        $this->model = new KeysModel();
        $this->time = new \CodeIgniter\I18n\Time;
     
       $this->model1 = new FuncationModel();
       $this->Funcation = $this->model1->Funcation();
       
       
     $findFuncation = $this->Funcation;
       
       
        $Hrs5 = $findFuncation->Hrs5;
        $Days1 = $findFuncation->Days1;
        $Days7 = $findFuncation->Days7;
        $Days15 = $findFuncation->Days15;
        $Days30 = $findFuncation->Days30;
        $Days60 = $findFuncation->Days60;
        $Currency = $findFuncation->Currency;
        
        
        
        $this->game_list = [
            'PUBG' => 'PUBG Mobile'
        ];
          $this->duration = [
            
            5 => '5 Hour &mdash; '.$Currency.$Hrs5.'/Device',
            24 => '1 Days &mdash; '.$Currency.$Days1.'/Device',
            168 => '7 Days &mdash; '.$Currency.$Days7.'/Device',
            360 => '15 Days &mdash; '.$Currency.$Days15.'/Device',
            720 => '30 Days &mdash; '.$Currency.$Days30.'/Device',
            1440 => '60 Days &mdash; '.$Currency.$Days60.'/Device',
           
        ];
        $this->price = [
          
            5 => (int)$Hrs5,
            24 => (int)$Days1,
            168 => (int)$Days7,
            360 => (int)$Days15,
            720 => (int)$Days30,
            1440 => (int)$Days60,
           
        ];
    }
    public function index()
    {
        $model = $this->model;
        $user = $this->user;
           if ($user->level != 1 && $user->level != 2)  {
            $keys = $model->where('registrator', $user->username)
                ->findAll();
        } else {
            $keys = $model->findAll();
        }
        $data = [
            'title' => 'Keys',
            'user' => $user,
            'keylist' => $keys,
            'time' => $this->time,
        ];
        return view('Keys/list', $data);
    }
    public function api_get_keys()
    {
        // ? API for DataTable Keys
        $model = $this->model;
        return $model->API_getKeys();
    }
    public function api_key_reset()
    {
        sleep(1);
        $model = $this->model;
        $keys = $this->request->getGet('userkey');
        $reset = $this->request->getGet('reset');
        $db_key = $model->getKeys($keys);
        $rules = [];
        if ($db_key) {
            $total = $db_key->devices ? explode(',', $db_key->devices) : [];
            $rules = ['devices_total' => count($total), 'devices_max' => (int) $db_key->max_devices];
            $user = $this->user;
            if ($db_key->devices and $reset) {
                if ($user->level == 1 or $db_key->registrator == $user->username) {
                    $model->set('devices', NULL)
                        ->where('user_key', $keys)
                        ->update();
                    $rules = ['reset' => true, 'devices_total' => 0, 'devices_max' => $db_key->max_devices];
                }
            } else {
            }
        }
        $data = [
            'registered' => $db_key ? true : false,
            'keys' => $keys,
        ];
        $real_response = array_merge($data, $rules);
        return $this->response->setJSON($real_response);
    }
    
    
    public function delete_key($key_id = false)
{
    $model = $this->model;
    if (!$key_id) {
        return redirect()->to('keys')->with('msgDanger', 'Invalid key ID');
    }
    $user = $this->user;
    $key = $this->model->find($key_id, 'id_keys');
    if (!$key) {
        return redirect()->to('keys')->with('msgDanger', 'Key not found');
    }
     if ($user->level!= 1 && $key->registrator!= $user->username) {
        return redirect()->to('keys')->with('msgDanger', 'Only OWNER or registrars can delete keys');
    }
    
    $this->model->delete($key_id);
    return redirect()->to('keys')->with('msgSuccess', 'Key deleted successfully');
}
public function unused(){
    echo  date('Y-m-d H:i:s');
    $user = $this->user;
    $model=$this->model;
    if ($user->level!= 1) {
        return redirect()->to('keys')->with('msgDanger', 'Only Owner can delete Unused keys');
    }
    $data=$model->where('expired_date ='.null)->delete();
    return redirect()->back()->with('msgSuccess', 'Successfully Delete Unused Keys');
    
}
public function expired(){
    echo  date('Y-m-d H:i:s');
    $user = $this->user;
    $model=$this->model;
    if ($user->level!= 1) {
        return redirect()->to('keys')->with('msgDanger', 'Only Owner can delete Expired keys');
    }
    $data=$model->where('expired_date <',  date('Y-m-d H:i:s'))->delete();
    return redirect()->back()->with('msgSuccess', 'Successfully Delete Expired Keys');
}
    public function edit_key($key = false)
    {
        if ($this->request->getPost()) return $this->edit_key_action();
        $msgDanger = "The user key no longer exists.";
        if ($key) {
            $dKey = $this->model->getKeys($key, 'id_keys');
            $user = $this->user;
            if ($dKey) {
                if ($user->level == 1 or $dKey->registrator == $user->username) {
                $validation = Services::validation();
                    
                    $data = [
                        'title' => 'Key',
                        'user' => $user,
                        'key' => $dKey,
                        'game_list' => $this->game_list,
                        'time' => $this->time,
                        'key_info' => getDevice($dKey->devices),
                        'messages' => setMessage('Please carefuly edit information'),
                        'validation' => $validation,
                    ];
                    
                  //  $data['key'] = $this->model->getKeys($keys, 'id_keys');
    
                    
                    return view('Keys/key_edit', $data);
                } else {
                    $msgDanger = "Restricted to this user key.";
                }
            }
        }
        return redirect()->to('keys')->with('msgDanger', $msgDanger);
    }
    private function edit_key_action()
    {
        $keys = $this->request->getPost('id_keys');
        $user = $this->user;
        $dKey = $this->model->getKeys($keys, 'id_keys');
        $game = implode(",", array_keys($this->game_list));
        if (!$dKey) {
            $msgDanger = "The user key no longer exists~";
        } else {
            if ($user->level == 1 or $dKey->registrator == $user->username) {
                $form_reseller = [
                    'status' => [
                        'label' => 'status',
                        'rules' => 'required|integer|in_list[0,1]',
                        'erros' => [
                            'integer' => 'Invalid {field}.',
                            'in_list' => 'Choose between list.'
                        ]
                    ]
                ];
                $form_owner = [
                    'id_keys' => [
                        'label' => 'keys',
                        'rules' => 'required|is_not_unique[keys_code.id_keys]|numeric',
                        'errors' => [
                            'is_not_unique' => 'Invalid keys.'
                        ],
                    ],
                    'game' => [
                        'label' => 'Games',
                        'rules' => "required|alpha_numeric_space|in_list[$game]",
                        'errors' => [
                            'alpha_numeric_space' => 'Invalid characters.'
                        ],
                    ],
                    'user_key' => [
                        'label' => 'User keys',
                        'rules' => "required|is_unique[keys_code.user_key,user_key,$dKey->user_key]",
                        'errors' => [
                            'is_unique' => '{field} has been taken.'
                        ],
                    ],
                    'duration' => [
                        'label' => 'duration',
                        'rules' => 'required|numeric|greater_than_equal_to[1]',
                        'errors' => [
                            'greater_than_equal_to' => 'Minimum {field} is invalid.',
                            'numeric' => 'Invalid day {field}.'
                        ]
                    ],
                    'max_devices' => [
                        'label' => 'devices',
                        'rules' => 'required|numeric|greater_than_equal_to[1]',
                        'errors' => [
                            'greater_than_equal_to' => 'Minimum {field} is invalid.',
                            'numeric' => 'Invalid max of {field}.'
                        ]
                    ],
                    'registrator' => [
                        'label' => 'registrator',
                        'rules' => 'permit_empty|alpha_numeric_space|min_length[4]'
                    ],
                    'expired_date' => [
                        'label' => 'expired',
                        'rules' => 'permit_empty|valid_date[Y-m-d H:i:s]',
                        'errors' => [
                            'valid_date' => 'Invalid {field} date.',
                        ]
                    ],
                    'devices' => [
                        'label' => 'device list',
                        'rules' => 'permit_empty'
                    ]
                ];
                 $form_admin = [
                    'id_keys' => [
                        'label' => 'keys',
                        'rules' => 'required|is_not_unique[keys_code.id_keys]|numeric',
                        'errors' => [
                            'is_not_unique' => 'Invalid keys.'
                        ],
                    ],
                    'user_key' => [
                        'label' => 'User keys',
                        'rules' => "required|is_unique[keys_code.user_key,user_key,$dKey->user_key]",
                        'errors' => [
                            'is_unique' => '{field} has been taken.'
                        ],
                    ],
                    'duration' => [
                        'label' => 'duration',
                        'rules' => 'required|numeric|greater_than_equal_to[1]',
                        'errors' => [
                            'greater_than_equal_to' => 'Minimum {field} is invalid.',
                            'numeric' => 'Invalid day {field}.'
                        ]
                    ],
                    'registrator' => [
                        'label' => 'registrator',
                        'rules' => 'permit_empty|alpha_numeric_space|min_length[4]'
                    ],
                    'expired_date' => [
                        'label' => 'expired',
                        'rules' => 'permit_empty|valid_date[Y-m-d H:i:s]',
                        'errors' => [
                            'valid_date' => 'Invalid {field} date.',
                        ]
                    ]
                ];
                if ($user->level == 1) {
                    // Owner full rules.
                    $form_rules = array_merge($form_reseller, $form_owner);
                    $devices = $this->request->getPost('devices');
                    $max_devices = $this->request->getPost('max_devices');
            
                    $data_saves = [
                        'game' => $this->request->getPost('game'),
                        'user_key' => $this->request->getPost('user_key'),
                        'duration' => $this->request->getPost('duration'),
                        'max_devices' => $max_devices,
                        'status' => $this->request->getPost('status'),
                        //'registrator' => $this->request->getPost('registrator'),
                        'expired_date' => $this->request->getPost('expired_date') ?: NULL,
                        'devices' => setDevice($devices, $max_devices),
                    ];
                       } elseif ($user->level == 2) {
                    // Admin rules.
                    $form_rules = array_merge($form_reseller, $form_admin);
            
                    $data_saves = [
                        'user_key' => $this->request->getPost('user_key'),
                        'duration' => $this->request->getPost('duration'),
                        'status' => $this->request->getPost('status'),
                        //'registrator' => $this->request->getPost('registrator'),
                        'expired_date' => $this->request->getPost('expired_date') ?: NULL,
                    ];
               } else {
                    // Reseller just status rules, you can set manually later.
                    $form_rules = $form_reseller;
                    $data_saves = ['status' => $this->request->getPost('status')];
                }
                if (!$this->validate($form_rules)) {
                    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the error');
                } else {
                    // * Data Updates
                    $this->model->update($dKey->id_keys, $data_saves);
                    return redirect()->back()->with('msgSuccess', 'User key successfuly updated!');
                }
            } else {
                $msgDanger = "Restricted to this user key~";
            }
        }
        return redirect()->to('keys')->with('msgDanger', $msgDanger);
    }
    public function generate()
    {
        if ($this->request->getPost())
            return $this->generate_action();
        $user = $this->user;
        $validation = Services::validation();
        
                
       $this->model1 = new FuncationModel();
       $this->Funcation = $this->model1->Funcation();
       $findFuncation = $this->Funcation;
        $Currency = $findFuncation->Currency;
        
  
        $message = setMessage("<i class='bi bi-wallet'></i> Total Balance : $Currency $user->saldo");
        if ($user->saldo <= 0) {
            $message = setMessage("Please top up to your Owner.", 'warning');
        }
        $data = [
            'title' => 'GENERATE-KEYS',
            'user' => $user,
            'time' => $this->time,
            'game' => $this->game_list,
            'NAMAN' => $Currency,
            'duration' => $this->duration,
            'price' => json_encode($this->price),
            'messages' => $message,
            'validation' => $validation,
        ];
        return view('Keys/generate', $data);
    }
private function generate_action()
{
    $user = $this->user;
    $game = $this->request->getPost('game');
    $maxdx = $this->request->getPost('max_devices');
    $drtn = $this->request->getPost('duration');
    $bulk = $this->request->getPost('bulk');
    $maxd = 0;
     if (($user->level == 1) || ($user->level ==2)) {
        $maxd = $maxdx;
    } else {
        $cmax = 5;
        if ($maxdx > $cmax) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! U can make max ' . $cmax . ' device');
        } else {
            $maxd = $maxdx;
        }
    }
    $getPrice = getPrice($this->price, $drtn, $bulk, $maxd);
    $game_list = implode(",", array_keys($this->game_list));
    $form_rules = [
        'game' => [
            'label' => 'Games',
            'rules' => "required|alpha_numeric_space|in_list[$game_list]",
            'errors' => [
                'alpha_numeric_space' => 'Invalid characters.'
            ],
        ],
        'duration' => [
            'label' => 'duration',
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid day {field}.'
            ]
        ],
        'max_devices' => [
            'label' => 'devices',
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid max of {field}.'
            ]
        ],
        'bulk' => [
            'label' => 'bulk',   
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid bulk licenses.'
            ]
        ],
    ];
    $validation = Services::validation();
    $reduceCheck = ($user->saldo - $getPrice); // *$bulk
    if ($reduceCheck < 0) {
        $validation->setError('bulk', 'Insufficient balance');
        return redirect()->back()->withInput()->with('msgWarning', 'Please top up to your Owner or Admin.');
    } else {
        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the error');
        } else {
            $licenses = [];
            for ($i = 0; $i < $bulk; $i++) {
                $license_token = random_string('alnum', 69);
                $license = random_string('alnum', 10);
                $licenses[] = [
                    'game' => $game,
                    'user_key' => $license,
                    'duration' => $drtn,
                    'max_devices' => $maxd,
                    'registrator' => $user->username,
                    'key_reset_time' => "3",
                    'key_reset_token' => $license_token,
                ];
            }
            // * reseller reduce saldo
            $this->userModel->update(session('userid'), ['saldo' => $reduceCheck]);
            $history = new HistoryModel();
            foreach ($licenses as $license) {
                $idKeys = $this->model->insert($license);
                $history->insert([
                    'keys_id' => $idKeys,
                    'user_do' => $user->username,
                    'info' => "$game|" . substr($license['user_key'], 0, 5) . "|$drtn|$maxd"
                ]);
            }
            $other_response = [
               'fees' => $getPrice,
               'user_key' => array_column($licenses, 'user_key'),
            ];
            $msg = "Successfuly Generated.";
            session()->setFlashdata(array_merge($license, $other_response));
            return redirect()->back()->with('msgSuccess', $msg);
          }
      }
   }
   
   
   
       public function generatex()
    {
        $user = $this->user;
        
        if ($this->request->getPost('custom'))
            return $this->generatex_action();
            
            if ($this->request->getPost('normal'))
            return $this->generatexx_action();
           
        $validation = Services::validation();
        
       $this->model1 = new FuncationModel();
       $this->Funcation = $this->model1->Funcation();
       $findFuncation = $this->Funcation;
        $Currency = $findFuncation->Currency;
        
        $message = setMessage("<i class='bi bi-wallet'></i> Total Balance : $Currency $user->saldo");
        if ($user->saldo <= 0) {
            $message = setMessage("Please top up to your Owner.", 'warning');
        }
        $data = [
            'title' => 'NAME-KEYS',
            'user' => $user,
            'time' => $this->time,
            'game' => $this->game_list,
            'NAMAN' => $Currency,
            'duration' => $this->duration,
            'price' => json_encode($this->price),
            'messages' => $message,
            'validation' => $validation,
        ];
        return view('Keys/Ngenerate', $data);
    }
private function generatex_action()
{
    $user = $this->user;
    $game = $this->request->getPost('game');
    $name = $this->request->getPost('name');
    $maxdx = $this->request->getPost('max_devices');
    $drtn = $this->request->getPost('duration');
    $bulk = $this->request->getPost('bulk');
    $maxd = 0;
     if (($user->level == 1) || ($user->level ==2)) {
        $maxd = $maxdx;
    } else {
        $cmax = 5;
        if ($maxdx > $cmax) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! U can make max ' . $cmax . ' device');
        } else {
            $maxd = $maxdx;
        }
    }
    $getPrice = getPrice($this->price, $drtn, $bulk, $maxd);
    $game_list = implode(",", array_keys($this->game_list));
    $form_rules = [
        'game' => [
            'label' => 'Games',
            'rules' => "required|alpha_numeric_space|in_list[$game_list]",
            'errors' => [
                'alpha_numeric_space' => 'Invalid characters.'
            ],
        ],
        'duration' => [
            'label' => 'duration',
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid day {field}.'
            ]
        ],
        'max_devices' => [
            'label' => 'devices',
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid max of {field}.'
            ]
        ],
        'bulk' => [
            'label' => 'bulk',   
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid bulk licenses.'
            ]
        ],
         'name' => [
                'label' => 'Name',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[10]',
                'errors' => [
                    'max_length' => 'The {field} has been to long.'
                ]
            ],
    ];
    $validation = Services::validation();
    $reduceCheck = ($user->saldo - $getPrice); // *$bulk
    if ($reduceCheck < 0) {
        $validation->setError('bulk', 'Insufficient balance');
        return redirect()->back()->withInput()->with('msgWarning', 'Please top up to your Owner or Admin.');
    } else {
        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the error');
        } else {
        // ... existing code ...
for ($i = 0; $i < $bulk; $i++) {
    $license_token = random_string('alnum', 69);
    $license = random_string('alnum', 5);
   
    // Fix: Properly concatenate name with random license
    $Namelicense = $name . $license;
        
    $licenses[] = [
        'game' => $game,
        'user_key' => $Namelicense,
        'duration' => $drtn,
        'max_devices' => $maxd,
        'registrator' => $user->username,
        'key_reset_time' => "3",
        'key_reset_token' => $license_token,
    ];
}
// ... existing code ...
            // * reseller reduce saldo
            $this->userModel->update(session('userid'), ['saldo' => $reduceCheck]);
            $history = new HistoryModel();
            foreach ($licenses as $license) {
                $idKeys = $this->model->insert($license);
                $history->insert([
                    'keys_id' => $idKeys,
                    'user_do' => $user->username,
                    'info' => "$game|" . substr($license['user_key'], 0, 5) . "|$drtn|$maxd"
                ]);
            }
            $other_response = [
               'fees' => $getPrice,
               'user_key' => array_column($licenses, 'user_key'),
            ];
            $msg = "Successfuly Generated.";
            session()->setFlashdata(array_merge($license, $other_response));
            return redirect()->back()->with('msgSuccess', $msg);
          }
      }
   }
   
   
   private function generatexx_action()
{
    $user = $this->user;
     if ($user->level != 1 && $user->level != 2) {
           return redirect()->back()->with('msgWarning', "Failed! You Can't Generate Name Key"); }
           
    $game = $this->request->getPost('game');
    $name = $this->request->getPost('name');
    $maxdx = $this->request->getPost('max_devices');
    $drtn = $this->request->getPost('duration');
   
    $maxd = 0;
     if (($user->level == 1) || ($user->level ==2)) {
        $maxd = $maxdx;
    } else {
        $cmax = 5;
        if ($maxdx > $cmax) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! U can make max ' . $cmax . ' device');
        } else {
            $maxd = $maxdx;
        }
    }
    $getPrice = getPricex($this->price, $drtn, $maxd);
    $game_list = implode(",", array_keys($this->game_list));
    $form_rules = [
        'game' => [
            'label' => 'Games',
            'rules' => "required|alpha_numeric_space|in_list[$game_list]",
            'errors' => [
                'alpha_numeric_space' => 'Invalid characters.'
            ],
        ],
        'duration' => [
            'label' => 'duration',
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid day {field}.'
            ]
        ],
        'max_devices' => [
            'label' => 'devices',
            'rules' => 'required|numeric|greater_than_equal_to[1]',
            'errors' => [
                'greater_than_equal_to' => 'Minimum {field} is invalid.',
                'numeric' => 'Invalid max of {field}.'
            ]
        ],
         'name' => [
                'label' => 'Name',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[10]',
                'errors' => [
                    'max_length' => 'The {field} has been to long.'
                ]
            ],
    ];
    $validation = Services::validation();
    $reduceCheck = ($user->saldo - $getPrice);
    if ($reduceCheck < 0) {
        $validation->setError('max_devices', 'Insufficient balance');
        return redirect()->back()->withInput()->with('msgWarning', 'Please top up to your Owner or Admin.');
    } else {
        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the error');
        } else {
            $licenses = [];
             {
                $license_token = random_string('alnum', 69);
                $license = random_string('alnum', 8);
   
                    $Namelicense = $name;
                    
                    $existingKey = $this->model->where('user_key', $Namelicense)->first();
                if ($existingKey) {
                    $validation->setError('name',"$Namelicense  User key already exists");
                    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please Check The Error in Name Key Section');
                }
                
                $licenses[] = [
                    'game' => $game,
                    'user_key' => $Namelicense,
                    'duration' => $drtn,
                    'max_devices' => $maxd,
                    'registrator' => $user->username,
                    'key_reset_time' => "3",
                    'key_reset_token' => $license_token,
                ];
            }
            // * reseller reduce saldo
            $this->userModel->update(session('userid'), ['saldo' => $reduceCheck]);
            $history = new HistoryModel();
            foreach ($licenses as $license) {
                $idKeys = $this->model->insert($license);
                $history->insert([
                    'keys_id' => $idKeys,
                    'user_do' => $user->username,
                    'info' => "$game|" . substr($license['user_key'], 0, 5) . "|$drtn|$maxd"
                ]);
            }
            $other_response = [
               'fees' => $getPrice,
               'user_key' => array_column($licenses, 'user_key'),
            ];
            $msg = "Successfuly Generated.";
            session()->setFlashdata(array_merge($license, $other_response));
            return redirect()->back()->with('msgSuccess', $msg);
          }
      }
   }
   
}