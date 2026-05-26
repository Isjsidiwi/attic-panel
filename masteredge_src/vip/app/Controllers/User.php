<?php
namespace App\Controllers;
use App\Models\CodeModel;
use App\Models\HistoryModel;
use App\Models\UserModel;
use App\Models\KeysModel;
use App\Models\FuncationModel;
use App\Models\FilesModel;
use CodeIgniter\Config\Services;
class User extends BaseController
{
    protected $model, $userid, $user;
    public function __construct()
    {
        $this->userid = session()->userid;
        $this->model = new UserModel();
        $this->user = $this->model->getUser($this->userid);
        $this->time = new \CodeIgniter\I18n\Time;
        
          $this->set_level = [
           2 => 'ADMIN PRO +',
           3 => 'ADMIN'
        ];
        
        
         $this->time1 = [
           'Hrs5' => '5 HOURS',
           'Days1' => '1 DAYS',
            'Days7' => '7 DAYS',
             'Days15' => '15 DAYS',
              'Days30' => '30 DAYS',
               'Days60' => '60 DAYS',
        ];
        
        $this->time2 = [
           '₹' => '₹ SET CURRENCY',
           '$' => '$ SET CURRENCY',
           '¥' => '¥ SET CURRENCY',
           '€' => '€ SET CURRENCY',
           '£' => '£ SET CURRENCY',
        ];
    }
    public function index()
    {
        $historyModel = new HistoryModel();
        $this->model1 = new FuncationModel();
       $this->Funcation = $this->model1->Funcation();
       $findFuncation = $this->Funcation;
        $Currency = $findFuncation->Currency;
        
        $data = [
            'title' => 'Dashboard',
            'user' => $this->user,
            'time' => $this->time,
            'NAMAN' => $Currency,
            'history' => $historyModel->getAll(),
        ];
        return view('User/dashboard', $data);
    }
    
    public function clear_history($id_history = false)
{
         $user  = $this->user;
         if ($user->level != 1)
      return redirect()->back()->with('msgWarning', "You Can't Clear History");
      
     $historyModel = new HistoryModel();
     $historyModel->truncate();
    return redirect()->back()->with('msgSuccess', 'All History Successfully Deleted');
}
  public function clear_referral()
{
         $user  = $this->user;
         if ($user->level != 1)
      return redirect()->back()->with('msgWarning', "You Can't delete referral");
      
     $CModel = new CodeModel();
     $CModel->truncate();
    return redirect()->back()->with('msgSuccess', 'All Referral Successfully Deleted');
}
public function clear_keys()
{
         $user  = $this->user;
         if ($user->level != 1)
      return redirect()->back()->with('msgWarning', "You Can't delete All KEYS");
      
     $KModel = new KeysModel();
     $KModel->truncate();
    return redirect()->back()->with('msgSuccess', 'All KEYS Successfully Deleted');
}
 public function clear()
    {
        $user  = $this->user;
         if ($user->level != 1 && $user->level != 2)
           return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
        $mCode = new CodeModel();
        $KCode = new KeysModel();
      $historyModel = new HistoryModel();
        $validation = Services::validation();
        $data = [
            'title' => 'CLEAR PANEL',
            'user' => $user,
            'time' => $this->time,
           'total_his' => $historyModel->countAllResults(),
          'total_keys' => $KCode->countAllResults(),
           'total_code' => $mCode->countAllResults(),
            'validation' => $validation
        ];
        return view('Admin/clear', $data);
    }
    public function ref_index()
    {
        $user  = $this->user;
         if ($user->level != 1 && $user->level != 2)
           return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
       
        if ($this->request->getPost())
           return $this->reff_action();
        $mCode = new CodeModel();
       $this->model1 = new FuncationModel();
       $this->Funcation = $this->model1->Funcation();
       $findFuncation = $this->Funcation;
        $Currency = $findFuncation->Currency;
        $validation = Services::validation();
        $data = [
            'title' => 'Referral',
            'user' => $user,
            'time' => $this->time,
            'set_level' => $this->set_level,
            'code' => $mCode->findAll(),
            'Naman'=> $Currency,
            'total_code' => $mCode->countAllResults(),
            'validation' => $validation
        ];
        return view('Admin/referral', $data);
    }
    private function reff_action()
    {
        $saldo = $this->request->getPost('set_saldo');
        $set_level = $this->request->getPost('set_level');
        $used_limit = $this->request->getPost('used_limit');
        $form_rules = [
            'set_saldo' => [
                'label' => 'saldo',
                'rules' => 'required|numeric|max_length[25]|greater_than_equal_to[0]',
                'errors' => [
                    'greater_than_equal_to' => 'Invalid currency, cannot set to minus.'
                ]
            ],
            'used_limit' => [
                'label' => 'USED LIMIT',
                'rules' => 'required|numeric|max_length[2]|greater_than_equal_to[0]',
                'errors' => [
                    'greater_than_equal_to' => 'Invalid LIMIT, cannot set to minus.'
                ]
            ],
            'set_level' => 'required',
        ];
        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed, check the form');
        } else {
            $code = random_string('alnum', 5);
            $codeHash = create_password($code, false);
            $referral_code = [
                'code' => $codeHash,
                'Ucode' => $code,
                'set_saldo' => ($saldo < 1 ? 0 : $saldo),
                'set_level' => $set_level,
                'max_limit' => $used_limit,
                'created_by' => session('unames')
            ];
            $mCode = new CodeModel();
            $ids = $mCode->insert($referral_code, true);
            if ($ids) {
                $msg = "Referral : $code";
                return redirect()->back()->with('msgSuccess', $msg);
            }
        }
    }
    public function api_get_users()
    {
        // API for DataTables
        $model = $this->model;
        return $model->API_getUser();
    }
    
    
    public function delete_user($userid = false)
{
    $user = $this->user;
    if ($user->level != 1 && $user->level != 2) {
        return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
    }
    
    $model = $this->model;
    $target = $model->getUser($userid);
    if (!$target) {
        $msg = "User no longer exists.";
        return redirect()->back()->with('msgDanger', $msg);
    }
    if ($target->level == 1) {
        $msg = " You Can't Delete OWNER.";
    return redirect()->back()->with('msgDanger', $msg);
    }
    
     if ($user->level == 2 && $target->level != 3) {
        $msg = " You Can't Delete Admin, Only Delete Reseller.";
        return redirect()->back()->with('msgDanger', $msg);
    }
    
    // Delete the user's image from the folder
    if ($target->image) {
        $image_path = ROOTPATH . 'public/uploads/' . $target->image;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    // Delete the user from the database
    $model->delete($userid);
    $msg = "User deleted successfully.";
    return redirect()->back()->with('msgSuccess', $msg);
}
    
    
    public function manage_users()
    {
        $user  = $this->user;
       if ($user->level != 1 && $user->level != 2)
            return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
        $model = $this->model;
       $this->model1 = new FuncationModel();
       $this->Funcation = $this->model1->Funcation();
       $findFuncation = $this->Funcation;
        $Currency = $findFuncation->Currency;
        $validation = Services::validation();
        $data = [
            'title' => 'Users',
            'user' => $user,
            'Naman' => $Currency,
            'user_list' => $model->getUserList(),
            'time' => $this->time,
            'validation' => $validation
        ];
        return view('Admin/users', $data);
    }
    public function user_edit($userid = false)
    {
        $user = $this->user;
        if ($user->level != 1 && $user->level != 2)
            return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
        if ($this->request->getPost())
            return $this->user_edit_action();
        $model = $this->model;
        $validation = Services::validation();
        $data = [
            'title' => 'EDIT USERS',
            'user' => $user,
            'target' => $model->getUser($userid),
            'user_list' => $model->getUserList(),
            'time' => $this->time,
            'validation' => $validation,
        ];
        return view('Admin/user_edit', $data);
    }
    private function user_edit_action()
    {
        $user = $this->user;
        $model = $this->model;
        $userid = $this->request->getPost('user_id');
        
        $target = $model->getUser($userid);
        if (!$target) {
            $msg = "User no longer exists.";
            return redirect()->to('dashboard')->with('msgDanger', $msg);
        }
        
         if ($target->level == 1) {
        $msg = " You Can't Edit OWNER.";
        return redirect()->back()->with('msgDanger', $msg);
    }
    
      if ($user->level == 2 && $target->level != 3) {
        $msg = " You Can't Edit Admin, Only Edit Reseller.";
        return redirect()->back()->with('msgDanger', $msg);
    }
        
        $username = $this->request->getPost('username');
        $form_rules = [
            'username' => [
                'label' => 'username',
                'rules' => "required|alpha_numeric|min_length[5]|max_length[111]|is_unique[users.username,username,$target->username]",
                'errors' => [
                    'is_unique' => 'The {field} has taken by other.'
                ]
            ],
            'fullname' => [
                'label' => 'name',
                'rules' => 'permit_empty|alpha_space|min_length[5]|max_length[111]',
                'errors' => [
                    'alpha_space' => 'The {field} only allow alphabetical characters and spaces.'
                ]
            ],
            'level' => [
                'label' => 'roles',
                'rules' => 'required|numeric|in_list[2,3]',
                'errors' => [
                    'in_list' => 'Invalid {field}.'
                ]
            ],
            'status' => [
                'label' => 'status',
                'rules' => 'required|numeric|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Invalid {field} account.'
                ]
            ],
            'saldo' => [
                'label' => 'saldo',
                'rules' => 'permit_empty|numeric|max_length[111]|greater_than_equal_to[0]',
                'errors' => [
                    'greater_than_equal_to' => 'Invalid currency, cannot set to minus.'
                ]
            ],
            
        ];
        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Something wrong! Please check the form');
        } else {
            $fullname = $this->request->getPost('fullname');
            $level = $this->request->getPost('level');
            $status = $this->request->getPost('status');
            $saldo = $this->request->getPost('saldo');
           // $uplink = $this->request->getPost('uplink');
            $data_update = [
                'username' => $username,
                'fullname' => esc($fullname),
                'level' => $level,
                'status' => $status,
                'saldo' => (($saldo < 1) ? 0 : $saldo),
               /// 'uplink' => $uplink,
            ];
            $update = $model->update($userid, $data_update);
            if ($update) {
                return redirect()->back()->with('msgSuccess', "Successfuly update $target->username.");
            }
        }
    }
    public function settings()
    {
        if ($this->request->getPost('password_form'))
            return $this->passwd_act();
        if ($this->request->getPost('fullname_form'))
            return $this->fullname_act();
            
            
      if ($this->request->getPost('image_form'))
            return $this->image_act();
        $user = $this->user;
        $validation = Services::validation();
        $data = [
            'title' => 'Settings',
            'user' => $user,
            'time' => $this->time,
            'validation' => $validation
        ];
        return view('User/settings', $data);
    }
    private function passwd_act()
    {
        $current = $this->request->getPost('current');
        $password = $this->request->getPost('password');
 
        $user = $this->user;
        $currHash = create_password($current, false);
        $validation = Services::validation();
        if (!password_verify($currHash, $user->password)) {
            $msg = "Wrong current password.";
            $validation->setError('current', $msg);
        } elseif ($current == $password) {
            $msg = "Nothing to change.";
            $validation->setError('password', $msg);
        }
        $form_rules = [
            'current' => [
                'label' => 'current',
                'rules' => 'required|min_length[5]|max_length[111]',
            ],
            'password' => [
                'label' => 'password',
                'rules' => 'required|min_length[5]|max_length[111]',
            ],
            'password2' => [
                'label' => 'confirm',
                'rules' => 'required|min_length[5]|max_length[111]|matches[password]',
                'errors' => [
                    'matches' => '{field} not match, check the {field}.'
                ]
            ],
        ];
        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Something wrong! Please check the form');
        } else {
            $newPassword = create_password($password);
            $this->model->update(session('userid'), ['password' => $newPassword]);
        
           return redirect()->back()->with('msgSuccess', 'Password Successfuly Changed.');
          
        }
    }
    private function fullname_act()
    {
        $user = $this->user;
        $newName = $this->request->getPost('fullname');
        if ($user->fullname == $newName) {
            $validation = Services::validation();
            $msg = "Nothing to change.";
            $validation->setError('fullname', $msg);
        }
        $form_rules = [
            'fullname' => [
                'label' => 'name',
                'rules' => 'required|alpha_space|min_length[5]|max_length[111]',
                'errors' => [
                    'alpha_space' => 'The {field} only allow alphabetical characters and spaces.'
                ]
            ]
        ];
        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form');
        } else {
            $this->model->update(session('userid'), ['fullname' => esc($newName)]);
            return redirect()->back()->with('msgSuccess', 'Account Detail Successfuly Changed.');
        }
    }
    
      public function profile()
    {
            
      if ($this->request->getPost('image_form'))
            return $this->image_act();
        $user = $this->user;
        $validation = Services::validation();
        $data = [
            'title' => 'PROFILE IMAGE',
            'user' => $user,
            'time' => $this->time,
            'validation' => $validation
        ];
        return view('User/profile', $data);
    }
 
    
    private function image_act()
   {
    $user = $this->user;
    $validation = Services::validation();
    if ($this->request->getFile('image')) {
        $image = $this->request->getFile('image');
        $imageName = $image->getName();
        $imagePath = ROOTPATH . 'public/uploads/' . $imageName;
        // Delete old image from database and folder
        $oldImage = $user->image;
        if ($oldImage) {
            unlink(ROOTPATH . 'public/uploads/' . $oldImage);
            $this->model->update(session('userid'), ['image' => null]);
        }
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image->getClientMimeType(), $allowedTypes)) {
            $validation->setError('image', 'Invalid file type. Only JPEG, PNG, and GIF are allowed.');
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form');
        }
        // Upload new image
        if (!$image->move(ROOTPATH . 'public/uploads/', $imageName)) {
            $validation->setError('image', $image->getErrorString());
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form');
        }
        // Update user image in database
        $this->model->update(session('userid'), ['image' => $imageName]);
        return redirect()->back()->with('msgSuccess', 'Image updated successfully!');
    } else {
        return redirect()->back()->with('msgDanger', 'No image selected!');
    }
}    
    
public function files()
{
    $user = $this->user;
    if ($user->level != 1) {
        return redirect()->to('dashboard')->with('msgWarning', "You Dan't Have Permission To Upload Lib Files!");
    }
    if ($this->request->getPost('files_form')) {
        return $this->file_act();
    }
    $filesModel = new FilesModel();
    $validation = Services::validation();
    $files = $filesModel->findAll();
    $data = [
        'title' => 'APPLICATION FILE',
        'user' => $user,
        'time' => $this->time,
        'files' => $files,
       'validation' => $validation
    ];
    return view('User/files', $data);
}
private function file_act()
{
    
    $validationRules = [
    'file' => [
        'label'  => 'File',
        'rules'  => 'uploaded[file]|max_size[file,10485760]|mime_in[file,application/octet-stream,application/x-sharedlib]',
        'errors' => [
            'uploaded' => 'Please choose a LIB file to upload.',
            'max_size' => 'The file you selected is too large. Maximum allowed Size is 10 MB.',
            'mime_in'  => 'Please select valid file type. Allowed file type example is: [libxxxx.so]',
        ],
    ],
];
    $validation = Services::validation();
        $validation->setRules($validationRules);
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form');
        }
    $fileModel = new FilesModel();
    $file = $this->request->getFile('file');
    $fileName = $file->getName();
    $file->move(ROOTPATH . 'public/file/', $fileName);
    // Get the old file from the database
    $oldFile = $fileModel->where('id', 1)->first(); // Assuming the file ID is 1
    // If the database is empty, insert the new file
    if (!$oldFile) {
        $fileModel->insert([
            'name' => $fileName,
            'path' => 'public/file/' . $fileName,
        ]);
    } else {
        
        
//if (file_exists($oldFile['path']) && is_file($oldFile['path'])) {
    //unlink($oldFile['path']);
//}
        //unlink($oldFile['path']);
        // Delete the old file
      
        // Update the file in the database
        
      unlink(ROOTPATH . 'public/file/' . $oldFile['name']);
        
        $fileModel->update($oldFile['id'], [
            'name' => $fileName,
            'path' => 'public/file/' . $fileName,
        ]);
    }
     return redirect()->back()->with('msgSuccess', 'LIB File Save Successfully.');
}
    public function price()
{
    $user = $this->user;
    if ($user->level != 1 && $user->level != 2) {
        return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
    }
    
  //  $time1 = ''; // 
    $new_value = ''; // 
  //  $time2 = ''; // 
    
    if ($this->request->getPost('price_form')) {
        return $this->price_act();
    }
        if ($this->request->getPost('money_form')) {
        return $this->money_act();
    }
    
    $funcationModel = new FuncationModel();
    $validation = Services::validation();
    $prices = $funcationModel->findAll();
    $data = [
        'title' => 'PRICE UPDATE',
        'user' => $user,
        'time' => $this->time,
        'time1' => $this->time1,
        'time2' => $this->time2,
        'prices' => $prices,
        'new_value' => $new_value,
        'validation' => $validation
    ];
    return view('Keys/Price', $data);
}
    
  private function price_act()
         {
     $user = $this->user;
    if ($user->level != 1) {
        return redirect()->back()->with('msgWarning', "You Can't Update Price Please Talk To Owner");
    } 
    // Validate the POST data
    $validationRule = [
        'time1' => 'required',
        'new_value' => 'required|integer|greater_than[0]',
    ];
    $validation = Services::validation();
    $validation->setRules($validationRule);
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form. Errors: ' . json_encode($errors));
    }
    
    // Get the POST data
    $time1 = $this->request->getPost('time1');
    $new_value = $this->request->getPost('new_value');
    // Define the fields to update
    $funcationModel = new FuncationModel();
    $result = $funcationModel->where('id_path', 1)->first();
    // Update only the selected field
    $fields = [];
    switch ($time1) {       
        case 'Hrs5':
            $fields['Hrs5'] = $new_value;
            break;
        case 'Days1':
            $fields['Days1'] = $new_value;
            break;
        case 'Days7':
            $fields['Days7'] = $new_value;
            break;
        case 'Days15':
            $fields['Days15'] = $new_value;
            break;
        case 'Days30':
            $fields['Days30'] = $new_value;
            break;
        case 'Days60':
            $fields['Days60'] = $new_value;
            break;
  
        // Add more time1 data fields here as needed
    }
    // Update the record
    $update = $funcationModel->update($result, $fields);
    if ($update) {
        return redirect()->back()->with('msgSuccess', $time1 . ' Updated successfully');
    } else {
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! An error occurred while updating the record.');
        }
    }
    
    
  private function money_act()
         {
    // Validate the POST data
    $validationRule = [
        'time2' => 'required',
    ];
    $validation = Services::validation();
    $validation->setRules($validationRule);
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form. Errors: ' . json_encode($errors));
    }
    // Get the POST data
    $time2 = $this->request->getPost('time2');
    // Define the fields to update
    $funcationModel = new FuncationModel();
    $result = $funcationModel->where('id_path', 1)->first();
    // Update the record
            $data2 = [
                      'Currency' => $time2,
                   ];
    $update = $funcationModel->update($result, $data2);
    if ($update) {
        return redirect()->back()->with('msgSuccess', $time2 . ' Currency Set Successfully');
    } else {
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! An error occurred while updating the record.');
        }
    }
    
 public function server()
     {
    $user = $this->user;
    if ($user->level != 1 && $user->level != 2) {
        return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
    }
    
    if ($this->request->getPost('online_form')) {
        return $this->online_act();
    }
        if ($this->request->getPost('name_form')) {
        return $this->name_act();
    }
     if ($this->request->getPost('server_form')) {
        return $this->server_act();
    }
    if ($this->request->getPost('ftext_form')) {
        return $this->ftext_act();
    }
    $funcationModel = new FuncationModel();
    $validation = Services::validation();
    $server = $funcationModel->findAll();
    $data = [
        'title' => 'GAME SERVER',
        'user' => $user,
        'time' => $this->time,
        'server' => $server,
        'validation' => $validation
    ];
    return view('Admin/Server', $data);
}
   private function online_act()
         {
   
    $validationRule = [
        'server' => 'required',
    ];
    $validation = Services::validation();
    $validation->setRules($validationRule);
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form. Errors: ' . json_encode($errors));
    }
   
    $online = $this->request->getPost('server');
    $value = $this->request->getPost('value');
    $funcationModel = new FuncationModel();
    $result = $funcationModel->where('id_path', 1)->first();
  
            $data2 = [
                      'Online' => $online,
                      'Maintenance' => $value,
                   ];
    $update = $funcationModel->update($result, $data2);
    if ($update) {
        return redirect()->back()->with('msgSuccess', 'SERVER Update Successfully');
    } else {
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! An error occurred while updating the record.');
        }
    }
 private function name_act()
         {
   
    $validationRule = [
        'name' => [
                'label' => 'MOD NAME',
                'rules' => 'required|alpha_space|min_length[5]|max_length[111]',
                'errors' => [
                    'alpha_space' => 'The {field} only allow alphabetical characters and spaces.'
                ]
            ]
    ];
    $validation = Services::validation();
    $validation->setRules($validationRule);
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form. Errors: ' . json_encode($errors));
    }
   
    $name = $this->request->getPost('name');
    $funcationModel = new FuncationModel();
    $result = $funcationModel->where('id_path', 1)->first();
  
            $data2 = [
                      'ModName' => $name,
                   ];
    $update = $funcationModel->update($result, $data2);
    if ($update) {
        return redirect()->back()->with('msgSuccess', $name . ' Mod Name Successfully Saved');
    } else {
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! An error occurred while updating the record.');
        }
    }
 private function server_act()
         {
   
        $validationRule = [
        'Bullet' => 'required',
         'Aimbot' => 'required',
          'Memory' => 'required',
          'SilentAim' => 'required',
         'item' => 'required',
          'Setting' => 'required',
           'Esp' => 'required',
    ];
    $validation = Services::validation();
    $validation->setRules($validationRule);
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form. Errors: ' . json_encode($errors));
    }
   
    $Bullet = $this->request->getPost('Bullet'); 
     $Aimbot = $this->request->getPost('Aimbot');
      $Memory = $this->request->getPost('Memory');
       $SilentAim = $this->request->getPost('SilentAim'); 
     $item = $this->request->getPost('item');
      $Setting = $this->request->getPost('Setting');
      $Esp = $this->request->getPost('Esp');
    $funcationModel = new FuncationModel();
    $result = $funcationModel->where('id_path', 1)->first();
  
            $data2 = [
                      'Bullet' => $Bullet,
                      'Aimbot' => $Aimbot,
                      'Memory' => $Memory,
                      'Esp' => $Esp,
                      'SilentAim' => $SilentAim,
                      'item' => $item,
                      'Setting' => $Setting,
                   ];
    $update = $funcationModel->update($result, $data2);
    if ($update) {
        return redirect()->back()->with('msgSuccess', 'Features Update Successfully');
    } else {
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! An error occurred while updating the record.');
        }
    }
 private function ftext_act()
         {
   
    $validationRule = [
        'status' => 'required',
        'ftext' => [
                'label' => 'FLOTING TEXT',
                'rules' => 'required|alpha_space|min_length[5]|max_length[111]',
                'errors' => [
                    'alpha_space' => 'The {field} only allow alphabetical characters and spaces.'
                ]
            ],
    ];
    $validation = Services::validation();
    $validation->setRules($validationRule);
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form. Errors: ' . json_encode($errors));
    }
   
    $status = $this->request->getPost('status');
    $ftext = $this->request->getPost('ftext');
    $funcationModel = new FuncationModel();
    $result = $funcationModel->where('id_path', 1)->first();
  
            $data2 = [
                      'status' => $status,
                      'ftext' => $ftext,
                   ];
    $update = $funcationModel->update($result, $data2);
    if ($update) {
        return redirect()->back()->with('msgSuccess', 'FLOATING & STATUS Update Successfully');
    } else {
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! An error occurred while updating the record.');
        }
    }
public function add_saldo()
{
    $user = $this->user;
    if ($user->level != 1 && $user->level != 2) {
        return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
    }
    if ($this->request->getPost()) {
        return $this->add_saldo_act();
    }
    $model = $this->model;
    $users = $model->getUserList();
    $validation = Services::validation();
    $this->model1 = new FuncationModel();
       $this->Funcation = $this->model1->Funcation();
       $findFuncation = $this->Funcation;
        $Currency = $findFuncation->Currency;
    $data = [
        'title' => 'Add Saldo',
        'user' => $user,
        'users' => $users,
        'time' => $this->time,
        'Naman' => $Currency,
        'validation' => $validation,
    ];
    return view('Admin/add_saldo', $data);
}
private function add_saldo_act()
{
    $user = $this->user;
    if ($user->level != 1) {
        return redirect()->back()->with('msgWarning', "You Can't Add Balance");
    }
    
    $validationRule = [
        'user_id' => 'required',
         'saldo' => [
                'label' => 'Balance',
                'rules' => 'required|numeric|max_length[10]|greater_than_equal_to[0]',
                'errors' => [
                    'greater_than_equal_to' => 'Invalid currency, cannot set to minus.'
                ]
            ],
    ];
    $validation = Services::validation();
    $validation->setRules($validationRule);
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form. Errors');
    }
    $user_id = $this->request->getPost('user_id');
    $saldo = $this->request->getPost('saldo');
    $model = $this->model;
    $target = $model->getUser($user_id);
    if (!$target) {
        $msg = "User no longer exists.";
        return redirect()->back()->with('msgDanger', $msg);
    }
    $data_update = [
        'saldo' => $target->saldo + $saldo,
    ];
    $update = $model->update($user_id, $data_update);
    if ($update) {
        return redirect()->back()->with('msgSuccess', "Balance added successfully to user $target->username");
    } else {
        return redirect()->back()->withInput()->with('msgDanger', 'Failed! An error occurred while updating the record');
       }
   }
}
