<?php

namespace App\Controllers\Auth;

use Delight\Auth\Role;
use Framework\Attributes\Route;
use Framework\Classes\Auth;
use Framework\Classes\Blade;
use Framework\Classes\Config;
use Framework\Classes\Mail;
use Framework\Classes\Validator;
use Framework\HTTP\Responses\JSONResponse;
use Framework\HTTP\Responses\RedirectResponse;

class AuthController {
    private $email_result = [];

    #[Route(['GET'], '/logout')]
    public function getLogout() {
        Auth::getInstance()->logOut();
        return new RedirectResponse(BASEURL);
    }

    #[Route(['POST'], '/login')]
    public function postLogin() {
        $val = Validator::getInstance()->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:5|max:255'
        ]);

        if ($val->fails()) {
            $errors = $val->errors()->all();
            $errors_str = implode(".\n", $errors);
            return new JSONResponse(['success' => false, 'message' => $errors_str]);
        }

        try {
            $remember = $_POST['remember'] ?? null;
            if ($remember != null) {
                $rememberDuration = (int) (60 * 60 * 24 * 365.25);
            } else {
                $rememberDuration = null;
            }

            Auth::getInstance()->login($_POST['email'], $_POST['password'], $rememberDuration);
            $_SESSION['full_login'] = true;
            unset($_SESSION['user_backup']);
            $next = $_POST['next'] ?? BASEURL;
            return new JSONResponse(['success' => true, 'action' => 'redirect', 'next' => urldecode($next), 'rem' => $rememberDuration]);
        } catch (\Delight\Auth\InvalidEmailException $e) {
            $data = ['success' => false, 'message' =>  'Invalid email address'];
            return new JSONResponse($data);
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            $data = ['success' => false, 'message' =>  'Invalid Password'];
            return new JSONResponse($data);
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            $data = ['success' => false, 'message' =>  'Email not verified'];
            return new JSONResponse($data);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            $data = ['success' => false, 'message' =>  'Too many requests! Try again later'];
            return new JSONResponse($data);
        }
    }

    #[Route(['POST'], '/register')]
    public function postRegister() {
        $val = Validator::getInstance()->validate($_POST, [
            'username' => 'required|min:4|max:24|alpha_num',
            'email' => 'required|email',
            'password' => 'required|min:5|max:255',
            'confirm_password' => 'required|same:password',
        ]);

        if ($val->fails()) {
            $errors = $val->errors()->all();
            $errors_str = implode(".\r\n", $errors);
            return new JSONResponse(['success' => false, 'message' => $errors_str]);
        }
        
        try {
            $auth = Auth::getInstance();

            if (Config::get('auth.verify')) {
                $userId = $auth->registerWithUniqueUsername($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
                    $message = Blade::view('_auth.emails.verify_email', ['token' => $token, 'selector' => $selector, 'url' => BASEURL]);
                    $this->email_result = Mail::sendEmail('Account Verification', $message, [$_POST['email'], $_POST['username']]);
                });
            } else {
                $userId = $auth->registerWithUniqueUsername($_POST['email'], $_POST['password'], $_POST['username'], null);
            }
            
            $auth->admin()->addRoleForUserById($userId, Role::CONSUMER);

            return new  JSONResponse(['email_result' => $this->email_result, 'success' => true, 'message' => 'User Has Been Registered.' . $this->email_result]);
        } catch (\Delight\Auth\InvalidEmailException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Invalid email address']);
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Invalid password']);
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            return new JSONResponse(['success' => false, 'message' => 'User already exists']);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Too many requests']);
        } catch (\Delight\Auth\DuplicateUsernameException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Username already taken.']);
        }
    }

    #[Route(['GET'], '/verify/{selector}/{token}')]
    function getVerify($selector, $token) {
        try {
            Auth::getInstance()->confirmEmail($selector, $token);

            return Blade::view('Auth.verify', ['success' => true, 'message' => 'Email address has been verified']);
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            return Blade::view('Auth.verify', ['success' => false, 'message' => 'Invalid token']);
        } catch (\Delight\Auth\TokenExpiredException $e) {
            return Blade::view('Auth.verify', ['success' => false, 'message' => 'Token expired']);
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            return Blade::view('Auth.verify', ['success' => false, 'message' => 'Email address already exists']);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return Blade::view('Auth.verify', ['success' => false, 'message' => 'Too many requests']);
        }
    }


    #[Route(['POST'], '/reconfirm')]
    function postReconfirm() {
        $val = Validator::getInstance()->validate($_POST, [
            'email' => 'required|email'
        ]);

        if ($val->fails()) {
            $errors = $val->errors()->all();
            $errors_str = implode("\n", $errors);
            return new JSONResponse(['success' => false, 'message' => $errors_str]);
        }

        try {
            $auth = Auth::getInstance();
            $auth->resendConfirmationForEmail($_POST['email'], function ($selector, $token) {
                $message = Blade::view('_auth.emails.verify_email', ['token' => $token, 'selector' => $selector, 'url' => BASEURL]);
                $this->email_result = Mail::sendEmail('Account Verfication', $message, [$_POST['email'], $_POST['email']]);
            });
            return new  JSONResponse(['email_result' => $this->email_result, 'success' => true, 'message' => 'Activation email has been sent.'. $this->email_result]);
        } catch (\Delight\Auth\ConfirmationRequestNotFound $e) {
            return new JSONResponse(['success' => false, 'message' => 'No earlier request found that could be re-sent']);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return new JSONResponse(['success' => false, 'message' => 'There have been too many requests -- try again later']);
        }
    }


    #[Route(['POST'], '/recover')]
    function postRecover() {
        $val = Validator::getInstance()->validate($_POST, [
            'email' => 'required|email'
        ]);

        if ($val->fails()) {
            $errors = $val->errors()->all();
            $errors_str = implode("\n", $errors);
            return new JSONResponse(['success' => false, 'message' => $errors_str]);
        }
        try {
            Auth::getInstance()->forgotPassword($_POST['email'], function ($selector, $token) {
                $message = Blade::view('_auth.emails.recover_password', ['token' => $token, 'selector' => $selector, 'url' => BASEURL]);
                $this->email_result = Mail::sendEmail('Password Recovery', $message, [$_POST['email'], $_POST['email']]);
            });
            return new  JSONResponse(['email_result' => $this->email_result, 'success' => true, 'message' => 'Request to change password has been registered.' . $this->email_result]);
        } catch (\Delight\Auth\InvalidEmailException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Invalid email address']);
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Email not verified']);
        } catch (\Delight\Auth\ResetDisabledException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Password reset is disabled']);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return new JSONResponse(['success' => false, 'message' => 'Too many requests']);
        }
    }

    #[Route(['GET'], '/reset/{selector}/{token}')]
    function getReset($selector, $token) {
        try {
            Auth::getInstance()->canResetPasswordOrThrow($selector, $token);

            return Blade::view('_auth.forms_manual.reset', ['success' => true, 'message' => 'Verified! Please update the password', 'selector' => $selector, 'token' => $token]);
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            return Blade::view('_auth.forms_manual.reset', ['success' => false, 'message' => 'Invalid token']);
        } catch (\Delight\Auth\TokenExpiredException $e) {
            return Blade::view('_auth.forms_manual.reset', ['success' => false, 'message' => 'Token expired']);
        } catch (\Delight\Auth\ResetDisabledException $e) {
            return Blade::view('_auth.forms_manual.reset', ['success' => false, 'message' => 'Password reset is disabled']);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return Blade::view('_auth.forms_manual.reset', ['success' => false, 'message' => 'Too many requests']);
        }
    }

    #[Route(['POST'], '/reset')]
    function postReset() {
        $val = Validator::getInstance()->validate($_POST, [
            'selector' => 'required',
            'token' => 'required',
            'password' => 'required|min:5|max:255',
            'confirm_password' => 'required|same:password',
        ]);

        if ($val->fails()) {
            $errors = $val->errors()->all();
            $errors_str = implode("\n", $errors);
            return new JSONResponse(['success' => false, 'message' => $errors_str]);
        }
        try {
            Auth::getInstance()->resetPassword($_POST['selector'], $_POST['token'], $_POST['password']);
            return new JSONResponse(['success' => true, 'message' => 'Password has been reset']);
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            return new JSONResponse(['success' => true, 'message' => 'Invalid token']);
        } catch (\Delight\Auth\TokenExpiredException $e) {
            return new JSONResponse(['success' => true, 'message' => 'Token expired']);
        } catch (\Delight\Auth\ResetDisabledException $e) {
            return new JSONResponse(['success' => true, 'message' => 'Password reset is disabled']);
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            return new JSONResponse(['success' => true, 'message' => 'Invalid password']);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return new JSONResponse(['success' => true, 'message' => 'Too many requests']);
        }
    }
}
