<?php

namespace Package\Controller;

use Package\Middleware\Token;
use Package\Model\User;
use Package\App\Input;
use Package\App\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {
  private $controller;

  public function __construct(){
    $this->controller = new User();
  }

  public function register(){
    $nama = htmlentities(trim(Input::get('Nama')));
    $email = htmlentities(trim(Input::get('Email')));
    $username = $this->generateAuthKey();
    $pass = password_hash(Input::get('Password'), PASSWORD_DEFAULT);
    $checkEmail = $this->check('Email', $email);
    if (!$checkEmail) {
      $register = $this->controller->insert([
        'Username' => $username,
        'Email' => $email,
        'Password' => $pass,
        'Nama' => $nama,
        'Avatar' => $this->getAvatar($email),
        'Token' => Token::generate('gost-crypto')
      ]);
      if ($register) {
        $data = $this->check('Username', $username);
        $this->setSession($data);
        Session::set('flashmsg', 'Terima kasih sudah bergabung di Peirtual, silahkan aktivasi akun anda !');
        redirect(baseurl().'/auth');
        return;
      }else {
        Session::set('flashmsg', 'Terjadi kesalahan saat mengisi data. silahkan coba lagi nanti.');
      }
    }else {
      Session::set('flashmsg', 'Email sudah terdaftar ! silahkan gunakan Email lain.');
    }
    redirect(baseurl().'/register');
  }

  public function logout(){
    Session::destroy();
    redirect(baseurl().'/login');
  }

  public function login(){
    $uname = trim(Input::get('Uname'));
    $pass = Input::get('Password');
    $login = (filter_var($uname, FILTER_VALIDATE_EMAIL)) ? 'Email' : 'Username';
    $data = $this->check($login, $uname);
    $url = (Session::get('verificationurl')) ? Session::get('verificationurl') : baseurl().'/home';
    if ($data) {
      if (password_verify($pass, $data->Password)) {
        $this->setSession($data);
        redirect($url);
        return;
      }else {
        Session::set('flashmsg', 'Password yang anda masukkan salah !');
      }
    }else {
      Session::set('flashmsg', 'Username atau Email belum terdaftar !');
    }
    redirect(baseurl().'/login');
  }

  public function verify(){
    Session::unset('verificationurl');
    $uname = Input::get('uname');
    $key = Input::get('authkey');
    $check = $this->check('Username', $uname);
    if (hash_equals($check->Token, $key)) {
      $verify = $this->controller->update('Username', $uname, ['Aktivasi' => 'True']);
      if ($verify) {
        Session::set([
          'userauth' => true,
          'flashmsg' => 'Selamat, Akun anda sudah diaktivasi !'
        ]);
        redirect(baseurl().'/home');
        return;
      }else {
        Session::set('flashmsg', 'Terjadi kesalahan saat proses Aktivasi. Silahkan coba lagi nanti !');
      }
    }else {
      Session::set('flashmsg', 'Token Aktivasi invalid. Gagal Aktivasi !');
    }
    redirect(baseurl().'/auth');
  }

  public function showProfile($username){
    $data = $this->check('Username', $username);
    if ($data) {
      view('user', 'desktop', [
        'id' => $data->Id,
        'username' => $data->Username,
        'usernama' => $data->Nama,
        'useremail' => $data->Email,
        'useravatar' => $data->Avatar,
        'userdesc' => $data->Deskripsi,
        'usertoken' => $data->Token
      ]);
    }else {
      http_response_code(404);
      view('404');
    }
  }

  public function changePassword(){
    $username = Session::get('username');
    $oldPassword = Input::get('OldPassword');
    $password = password_hash(Input::get('Password'), PASSWORD_DEFAULT);;
    $check = $this->check('Username', $username);
    if (password_verify($oldPassword, $check->Password)) {
      $changePass = $this->controller->update('Username', $username, [
        'Password' => $password
      ]);
      if ($changePass) {
        Session::set('flashmsg', 'Password anda berhasil di ganti !');
      }
    }else {
      Session::set('errmsg', 'Password anda salah !');
    }
    redirect(baseurl()."/users/{$username}#password");
  }

  public function edit(){
    $id = Input::get('id');
    $username = \htmlentities(trim(strtolower(Input::get('username'))));
    $nama = htmlentities(trim(Input::get('nama')));
    $deskripsi = htmlentities(trim(Input::get('deskripsi')));
    if (csrfverify()) {
      $check = $this->check('Username', $username);
      if (!$check || (Session::get('username') == $username)) {
        $update = $this->controller->update('Id', $id, [
          'Username' => $username,
          'Nama' => $nama,
          'Deskripsi' => $deskripsi
        ]);
        if ($update) {
          Session::set([
            'username' => $username,
            'usernama' => $nama
          ]);
          die(json_encode([
            'status' => true,
            'msg' => 'Edit Profile Sukses !'
          ]));
        }
      }else if ($check) {
        die(json_encode([
          'status' => false,
          'msg' => 'Username sudah terdaftar, silahkan cari Username lain !'
        ]));
      }
    }else {
      http_response_code(403);
      die(json_encode([
        'status' => http_response_code(),
        'msg' => 'Authorisasi gagal. Token invalid !'
      ]));
    }
  }

  public function listAllUsers(){
    die($this->controller->listAll('Nama, Username, Email, Avatar, Deskripsi'));
  }

  private function check($field, $value){
    return $this->controller->get([$field => ['=' => $value]]);
  }

  private function setSession($user){
    Session::set([
      'userlogin' => true,
      'userid' => $user->Id,
      'username' => $user->Username,
      'usernama' => $user->Nama,
      'useremail' => $user->Email,
      'useravatar' => $user->Avatar,
      'userauth' => (strtolower($user->Aktivasi) === 'true') ? true : false,
      'usertoken' => $user->Token,
    ]);
  }

  public function loadMoreUsers(){
    $start = (int) Input::get('startdata');
    $total = (int) Input::get('totaldata');
    die($this->controller->listLimit($start, $total));
  }

  private function generateAuthKey($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    if (!$this->check('Username', $randomString)) return $randomString;
    else $this->generateAuthKey();
  }

  private function getAvatar($email){
    return "https://www.gravatar.com/avatar/".sha1($email)."?d=monsterid";
  }

  public function sendAccountVerification(){
    $mail = new PHPMailer(true);
    $link = baseurl().'/verification?uname='.Session::get('username').'&authkey='.Session::get('usertoken');
    try{
      // $mail->SMTPDebug = 2 ;
      $mail->IsSMTP();
      $mail->SMTPSecure = 'ssl';
      $mail->Host = "smtp.gmail.com";
      $mail->Port = 465;
      $mail->SMTPAuth = true;
      $mail->Username = "itbitfest@gmail.com";
      $mail->Password = 'itfest2018';
      $mail->SetFrom("noreply@peirtual.com", "Sys Peirtual");
      $mail->AddAddress(Session::get('useremail'), Session::get('usernama'));  //tujuan email
      $mail->isHTML(true);
      $mail->Subject = "Aktivasi Akun"; //subyek email
      $mail->Body = "Silahkan klik link ini untuk mengaktivasi akun Piertual anda <a href='{$link}'>{$link}</a>";
      $mail->send();
      Session::set([
        'mailmsg' => 'Kode Aktivasi Sudah dikirim ke &lt;'.Session::get('useremail').'&gt;. Silahkan check email anda.'
      ]);
    }catch(Exception $e) {
      Session::set('mailmsg', 'Terjadi Kesalahan. Gagal mengirim email. Error Status: '.$e->getMessage());
    }
    redirect(baseurl().'/auth');
  }

}
?>
