<?php
// This is the model of the User Api
namespace Controllers\Api;

use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\General;
use Models\Api\User as UserModel;
use App\Exceptions\UserExceptions;

class User
{
    public function get(string $username) : array
    {
        $user = new UserModel();
        try {
            return $user->get($username);
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    public function create(array $data, string $provider) : void
    {
        if ($provider === 'local') {
            $this->createLocalUser($data);
        } elseif ($provider === 'azure') {
            $this->createAzureUser($data);
        } elseif ($provider === 'google') {
            $this->createGoogleUser($data);
        } elseif ($provider === 'mslive') {
            $this->createMsLiveUser($data);
        } else {
            Output::error('Invalid provider', 400);
        }
    }
    public function createAzureUser(array $data) : void
    {
        // $data is the contents of the JWT token, so we need to do some transformations before we can use it
        $insertData = [];
        // usernames comes as preferred_username in the JWT token
        $insertData['username'] = $data['preferred_username'] ?? Output::error('Missing preferred_username in token', 400);
        // Prepare the email, if it's not present in the JWT token, use the username
        $insertData['email'] = $data['email'] ?? $insertData['username'];
        // Name comes as name in the JWT token
        $insertData['name'] = $data['name'] ?? Output::error('Missing name in token', 400);
        // Last IPs comes as ipaddr in the JWT token, if it's not present, use the current IP
        $insertData['last_ips'] = $data['ipaddr'] ?? General::currentIP();
        // If JWT has a claim called 'ctry' take it, otherwise take the browser language
        $insertData['origin_country'] = (isset($data['ctry'])) ? $data['ctry'] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        // Role comes as an array of roles in the JWT token, we only need the first one
        $insertData['role'] = $data['roles'][0] ?? 'user';
        // Theme is set to the default color scheme
        $insertData['theme'] = COLOR_SCHEME;
        $insertData['provider'] = 'azure';
        $insertData['enabled'] = 1;

        $allowedData = ['username', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $insertData);

        $checks->checkParams($allowedData, $insertData);

        try {
            $createUser = new UserModel();
            $createUser->create($insertData);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    public function createMsLiveUser(array $data)
    {
        // $data is the contents of the JWT token, so we need to do some transformations before we can use it
        $insertData = [];
        // usernames comes as preferred_username in the JWT token
        $insertData['username'] = $data['preferred_username'] ?? Output::error('Missing preferred_username in token', 400);
        // Prepare the email, if it's not present in the JWT token, use the username
        $insertData['email'] = $data['email'] ?? $insertData['username'];
        // Name comes as name in the JWT token
        $insertData['name'] = $data['name'] ?? Output::error('Missing name in token', 400);
        // Last IPs comes as ipaddr in the JWT token, if it's not present, use the current IP
        $insertData['last_ips'] = General::currentIP();
        // If JWT has a claim called 'ctry' take it, otherwise take the browser language
        $insertData['origin_country'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        // Role comes as an array of roles in the JWT token, we only need the first one
        $insertData['role'] = 'user'; // There are no roles in live id tokens
        // Theme is set to the default color scheme
        $insertData['theme'] = COLOR_SCHEME;
        $insertData['provider'] = 'mslive';
        $insertData['enabled'] = 1;

        $allowedData = ['username', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $insertData);

        $checks->checkParams($allowedData, $insertData);

        try {
            $createUser = new UserModel();
            $createUser->create($insertData);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    public function createGoogleUser(array $data)
    {
        // $data is the contents of the JWT token, so we need to do some transformations before we can use it
        $insertData = [];
        // usernames comes as preferred_username in the JWT token
        $insertData['username'] = $data['email'] ?? Output::error('Missing email in token', 400);
        // Prepare the email, if it's not present in the JWT token, use the username
        $insertData['email'] = $data['email'];
        // Name comes as name in the JWT token
        $insertData['name'] = $data['name'] ?? Output::error('Missing name in token', 400);
        // Last IPs comes as ipaddr in the JWT token, if it's not present, use the current IP
        $insertData['last_ips'] = General::currentIP();
        // If JWT has a claim called 'ctry' take it, otherwise take the browser language
        $insertData['origin_country'] = (isset($data['locale'])) ? $data['locale'] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        // Role comes as an array of roles in the JWT token, we only need the first one
        $insertData['role'] = $data['roles'][0] ?? 'user';
        // Theme is set to the default color scheme
        $insertData['theme'] = COLOR_SCHEME;
        $insertData['picture'] = $data['picture'] ?? null;
        $insertData['provider'] = 'google';
        $insertData['enabled'] = 1;

        $allowedData = ['username', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $insertData);

        $checks->checkParams($allowedData, $insertData);

        try {
            $createUser = new UserModel();
            $createUser->create($insertData);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    public function createLocalUser(array $data) : void
    {
        if (!isset($data['email'])) {
            $data['email'] = $data['username'];
        }

        $allowedData = ['username', 'password', 'confirm_password', 'email', 'name', 'last_ips', 'origin_country', 'role', 'theme', 'provider', 'enabled'];

        $checks = new Checks($allowedData, $data);

        $checks->checkParams($allowedData, $data);


        if ($data['password'] !== $data['confirm_password']) {
            Output::error('Passwords do not match', 400);
        }

        unset($data['confirm_password']);

        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $createUser = new UserModel();
            $createUser->create($data);
            echo Output::success('User created');
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    public function update(array $data, int $id) : void
    {
        $user = new UserModel();
        try {
            $user->update($data, $id);
            echo Output::success('User updated');
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    public function delete(int $id) : void
    {
        $user = new UserModel();
        try {
            $user->delete($id);
            echo Output::success('User deleted');
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
    public function updateLastLogin(string $username) : void
    {
        // First check if the user exists
        $updatedUser = new UserModel();
        $updatedUserArray = $updatedUser->get($username);

        try {
            $updatedUser->update(['last_login' => date('Y-m-d H:i:s')], $updatedUserArray['id']);
        } catch (UserExceptions $e) {
            Output::error($e->getMessage());
        } catch (\Exception $e) {
            Output::error($e->getMessage());
        }
    }
}
