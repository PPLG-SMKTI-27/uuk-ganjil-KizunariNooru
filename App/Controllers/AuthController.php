<?php
require_once __DIR__ . '/../Models/User.php';
class AuthController {
  public function loginView(){ include __DIR__ . '/../Views/auth/login.php'; }
  public function proses(){
    if(!isset($_POST['email'])){ header('Location: ../Public/index.php'); exit; }
    $u = new User();
    $user = $u->verifyLogin($_POST['email'], $_POST['password']);
    if($user){
      $_SESSION['user'] = $user;
      $_SESSION['flash'] = ['type'=>'success','msg'=>'Login berhasil'];
      switch($user['role']){
        case 'Siswa': header('Location: ../Public/index.php?c=siswa&m=dashboard'); break;
        case 'WaliKelas': header('Location: ../Public/index.php?c=wali&m=index'); break;
        default: header('Location: ../Public/index.php?c=admin&m=index'); break;
      }
      exit;
    } else {
      $error = 'Email atau password salah';
      include __DIR__ . '/../Views/auth/login.php';
    }
  }
  public function logout(){ session_destroy(); header('Location: ../Public/index.php'); }
}
