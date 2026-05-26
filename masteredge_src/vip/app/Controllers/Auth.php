<?php


namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\UserModel;
use CodeIgniter\Config\Services;
class Auth extends BaseController
{
    protected $user;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function index()
    {
        
        $a = $this->userModel->getUser(session('userid'));
        dd($a, session());
    }
public function reset()
    {
        if (session()->has('userid'))
            return redirect()->to('dashboard');
        if ($this->request->getPost())
            return $this->reset_action();
        $data = [
            'title' => 'Reset',
            'validation' => Services::validation(),
        ];
        return view('Auth/reset', $data);
    }
    
    
    public function login()
    {
        if (session()->has('userid'))
            return redirect()->to('dashboard');
        if ($this->request->getPost())
            return $this->login_action();
        $data = [
            'title' => 'Login',
            'validation' => Services::validation(),
        ];
        return view('Auth/login', $data);
    }
    public function register()
    {
        if (session()->has('userid'))
            return redirect()->to('dashboard');
        if ($this->request->getPost())
            return $this->register_action();
        $data = [
            'title' => 'Register',
            'validation' => Services::validation(),
        ];
        return view('Auth/register', $data);
    }
    private function login_action()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $stay_log = $this->request->getPost('stay_log');
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$whatIWant = substr($user_agent, strpos($user_agent, "(") + 1);    
$whatIWantx = substr($whatIWant, strpos($whatIWant, ")") + 1);    
$whatIWantx = str_replace($whatIWantx,"",$whatIWant);
$whatIWantx = str_replace(")","",$whatIWantx);
$whatIWantx = str_replace(" ","",$whatIWantx);
$whatIWantx = str_replace(";","-",$whatIWantx);
        $form_rules = [
            'username' => [
                'label' => 'username',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[111]|is_not_unique[users.username]',
                'errors' => [
                    'is_not_unique' => 'The {field} is not registered.'
                ]
            ],
            'password' => [
                'label' => 'password',
                'rules' => 'required|min_length[5]|max_length[111]',
            ],
            'stay_log' => [
                'rules' => 'permit_empty|max_length[3]'
            ]
        ];
        if (!$this->validate($form_rules)) {
            return redirect()->route('login')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.');
        } else {
            $validation = Services::validation();
            $cekUser = $this->userModel->getUser($username, 'username');
            if ($cekUser) {
                $hashPassword = create_password($password, false);
                if (password_verify($hashPassword, $cekUser->password) or $hashPassword==create_passwords($password)) {
                    
              
                  
  if ($cekUser->loginDevices == NULL || $whatIWantx == $cekUser->loginDevices || $cekUser->loginDevices == "NAMANSINGH") {
if ($cekUser->loginDevices == NULL || $cekUser->loginDevices == "NAMANSINGH") { $this->userModel->set('loginDevices', $whatIWantx)->where('username', $username)->update(); }                   
                  
                  
                  
                  
                        
                  
                    
                    
                    
                    $time = new \CodeIgniter\I18n\Time;
                    $data = [
                        'userid' => $cekUser->id_users,
                        'unames' => $cekUser->username,
                        'time_login' => $stay_log ? $time::now()->addHours(24) : $time::now()->addMinutes(30),
                        'time_since' => $time::now(),
                    ];
                    session()->set($data);
                    return redirect()->to('dashboard');
                    
                    
                    } else {
                        
                      $validation->setError('password', 'Wrong device, please try again.');
                    return redirect()->route('login')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.'); 
                        
                    }
                    
                    
                } else {
                    $validation->setError('password', 'Wrong password, please try again.');
                    return redirect()->route('login')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.');
                }
            }
        }
    }
public function reset_action()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
    
        $form_rules = [
            'username' => [
                'label' => 'username',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[111]|is_not_unique[users.username]',
                'errors' => [
                    'is_not_unique' => 'The {field} is not registered.'
                ]
            ],
            'password' => [
                'label' => 'password',
                'rules' => 'required|min_length[5]|max_length[111]',
            ],
            'stay_log' => [
                'rules' => 'permit_empty|max_length[3]'
            ]
        ];
        if (!$this->validate($form_rules)) {
            return redirect()->route('reset')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.');
        } else {
            $validation = Services::validation();
            $cekUser = $this->userModel->getUser($username, 'username');
            if ($cekUser) {
                $hashPassword = create_password($password, false);
                if (password_verify($hashPassword, $cekUser->password)) {
                
             $checkXnx = false;       
             $checkTime = "";
             
             if ($cekUser->loginRsetTime == "3"){  $checkXnx = true; $checkTime = "2";
                $this->userModel->set('loginRsetTime', "2")->where('username', $username)->update();
             } else if ($cekUser->loginRsetTime == "2"){  $checkXnx = true; $checkTime = "1";
                $this->userModel->set('loginRsetTime', "1")->where('username', $username)->update();
             } else if ($cekUser->loginRsetTime == "1"){  $checkXnx = true; $checkTime = "0";
                $this->userModel->set('loginRsetTime', "0")->where('username', $username)->update(); } else {  $checkXnx = false;  }
            
            
            if ($checkXnx) {
              $this->userModel->set('loginDevices', NULL)->where('username', $username)->update();
                sleep(1);         
      return redirect()->back()->with('msgSuccess', '<strong>Successful reset!</strong> <p style="font-size:15px ; ">Available reset time : '.$checkTime.' only');             
            } else {
                sleep(1);         
      return redirect()->back()->with('msgWarning', '<strong>Unsuccessful reset!</strong> <p style="font-size:16px ; ">Reason : reset limit is end');            
            }
            
                    
                    
                } else {
                    $validation->setError('password', 'Wrong password, please try again.');
                    return redirect()->route('reset')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.');
                }
            }
        }
    }
    public function register_action()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $referral = $this->request->getPost('referral');
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$whatIWant = substr($user_agent, strpos($user_agent, "(") + 1);    
$whatIWantx = substr($whatIWant, strpos($whatIWant, ")") + 1);    
$whatIWantx = str_replace($whatIWantx,"",$whatIWant);
$whatIWantx = str_replace(")","",$whatIWantx);
$whatIWantx = str_replace(" ","",$whatIWantx);
$whatIWantx = str_replace(";","-",$whatIWantx);
        $form_rules = [
            'username' => [
                'label' => 'username',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[10]|is_unique[users.username]',
                'errors' => [
                    'is_unique' => 'The {field} has been taken.'
                ]
            ],
            'password' => [
                'label' => 'password',
                'rules' => 'required|min_length[4]|max_length[15]',
            ],
            'password2' => [
                'label' => 'password',
                'rules' => 'required|min_length[4]|max_length[15]|matches[password]',
                'errors' => [
                    'matches' => '{field} not match, check the {field}.'
                ]
            ],
            'referral' => [
                'label' => 'referral',
                'rules' => 'required|min_length[5]|alpha_numeric',
            ],
            'image' => 'uploaded[image]|mime_in[image,image/jpg,image/jpeg,image/png]|max_size[image,1024]',
        ];
        
        
        
        if (!$this->validate($form_rules)) {
            // Form Invalid
        } else {
            $mCode = new CodeModel();
            $rCheck = $mCode->checkCode($referral);
            $validation = Services::validation();
            if (!$rCheck) {
          $validation->setError('referral', 'Wrong referral, please try again.');
            } else {
                   $usedLimit = $rCheck->used_limit; 
                if ($usedLimit >= $rCheck->max_limit) { 
                    $validation->setError('referral', 'Referral Limit is Over, Please Try Another.');
                } else {
                    $hashPassword = create_password($password);
                    $image = $this->request->getFile('image');
                    $newName = $image->getRandomName();
                    $image->move(ROOTPATH . 'public/uploads', $newName);
                    
                    $data_register = [
                        'username' => $username,
                        'password' => $hashPassword,
                        'saldo' => $rCheck->set_saldo ?: 0,
                        'level' => $rCheck->set_level,
                        'uplink' => $rCheck->created_by,
                        'image' => $newName,
                        'loginDevices' => $whatIWantx,
                        'loginRsetTime' => "3"
                    ];          
                    $ids = $this->userModel->insert($data_register, true);
                    if ($ids) {
                        $mCode->useReferral($referral);
                        $msg = "Register Successfuly!";
                        return redirect()->to('login')->with('msgSuccess', $msg);
                    }
                }
            }
        }
        
        return redirect()->route('register')->withInput()->with('msgDanger', '<strong>Failed! </strong>  Please check the form.');
    }
    public function logout()
    {
        if (session()->has('userid')) {
            $unset = ['userid', 'unames', 'time_login', 'time_since'];
            session()->remove($unset);
            session()->setFlashdata('msgSuccess', 'Logout successfuly.');
        }
        return redirect()->to('login');
    }
}