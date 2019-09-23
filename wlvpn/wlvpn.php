<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_-@#$%^&*';
    $pass = '';
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $pass .= $alphabet[rand(0, $alphaLength)];
    }
    return $pass;
}

function genUsername($userId, $accountId) {
    return 'user_' . $userId . $accountId;
}

/**
 * Configuration for Reseller API Module
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-879
 *
 * @return array Config options
 *
 *
 */
function wlvpn_ConfigOptions()
{
    return [
        "api_key" => [
            "FriendlyName" => "API Key",
            "Type" => "text",
            "Size" => 25,
            "Description" => "API Key for reseller API",
            "Default" => "APIKEY"
        ],
        "api_endpoint" => [
            "FriendlyName" => "API Endpoint",
            "Type" => "text",
            "Size" => 100,
            "Description" => "Reseller API endpoint",
            "Default" => "https://api.wlvpn.com/v2/"
        ],
        "default_group_id" => [
            "FriendlyName" => "Group ID",
            "Type" => "text",
            "Default" => "2"
        ]

    ];
}

/**
 *  Function which set the MetaData for the plugin in admin side
 *
 * @author Pradeesh Kumar <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 * @return array
 */
function wlvpn_MetaData()
{
    return array(
        "DisplayName" => "White Label VPN Reseller"
    );
}

/**
 * Function which gets called when an order is placed.
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-879
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 *
 * @param array $params
 * @return string
 */
function wlvpn_CreateAccount(array $params)
{

    try {
        $data = array();
        $username = !empty($params['username']) ? $params['username'] : (genUsername($params['userid', $params['accountid']));
        $data['cust_user_id'] = $username;
        $data['cust_password'] = randomPassword();
        $data['acct_group_id'] = $params['configoption3'];

        $return = wlvpn_call_reseller_api("POST", $data, $params['configoption2'] . "customers",
            $params['configoption1']);
        $return = json_decode($return, true);
        if ($return && $return['api_status'] === 1) {
            $_SESSION['wlvpn_username'] = $data['cust_user_id'];
            $_SESSION['wlvpn_password'] = $data['cust_password'];
            $_SESSION['wlvpn_acct_id'] = $params['accountid'];
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'wlvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

/**
 * This function is called when a user account is suspended
 * either by admin or by cron for not paying the invoice
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 *
 * @param $params
 * @return string
 */
function wlvpn_SuspendAccount($params)
{
    try {
        $data = array();
        $data['action'] = 'suspend';
        $params['username'] = $params['username'] ? $params['username'] : (genUsername($params['userid', $params['accountid']));

        wlvpn_call_reseller_api("PUT", $data,
            $params['configoption2'] . "customers/" . $params['username'] . "/update", $params['configoption1']);
    } catch (Exception $e) {
        logModuleCall(
            'wlvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

/**
 * This function is called when the suspension is revoked
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 *
 * @param $params
 * @return string
 */
function wlvpn_UnsuspendAccount($params)
{
    try {
        $data = array();
        $data['action'] = 'unsuspend';
        $params['username'] = $params['username'] ? $params['username'] : (genUsername($params['userid', $params['accountid']));

        wlvpn_call_reseller_api("PUT", $data,
            $params['configoption2'] . "customers/" . $params['username'] . "/update", $params['configoption1']);
    } catch (Exception $e) {
        logModuleCall(
            'wlvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

/**
 * Function is called when an account is terminated by
 * either client or the administrator
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 *
 * @param $params
 * @return string
 */
function wlvpn_TerminateAccount($params)
{
    try {
        $data = array();
        $data['action'] = 'terminate';
        $params['username'] = $params['username'] ? $params['username'] : (genUsername($params['userid', $params['accountid']));

        wlvpn_call_reseller_api("PUT", $data,
            $params['configoption2'] . "customers/" . $params['username'] . "/update", $params['configoption1']);
    } catch (Exception $e) {

        logModuleCall(
            'wlvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

/**
 * This function is called when the account is renewed
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 *
 * @param $params
 * @return string
 */
function wlvpn_Renew($params)
{
    try {
        $data = array();
        $data['action'] = 'renew';
        $params['username'] = $params['username'] ? $params['username'] : (genUsername($params['userid', $params['accountid']));

        wlvpn_call_reseller_api("PUT", $data, $params['configoption2'] . "customers/" . $params['username'] . "/update",
            $params['configoption1']);
    } catch (Exception $e) {

        logModuleCall(
            'wlvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

/**
 * This function is called when package is either upgraded or downgraded
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 *
 * @param $params
 * @return string
 */

function wlvpn_ChangePackage($params)
{
    try {
        $data = array();
        $data['action'] = 'password';
        $data['cust_password'] = $params['password'];
        $params['username'] = $params['username'] ? $params['username'] : (genUsername($params['userid', $params['accountid']));

        wlvpn_call_reseller_api("PUT", $data, $params['configoption2'] . "customers/" . $params['username'] . "/update",
            $params['configoption1']);
    } catch (Exception $e) {
        logModuleCall(
            'wlvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

function wlvpn_ClientArea($params)
{
    return array(
        'templatefile' => 'clientarea',
        'vars' => array(),
    );
}

function wlvpn_ClientAreaCustomButtonArray()
{
    $buttonarray = array(
        "Reset Password" => "resetpassword",
    );
    return $buttonarray;
}

function wlvpn_resetPassword($params)
{
    $id = $_POST['id'];
    $clientid = $_SESSION['uid'];
    if ($id && $clientid) {
        $order = WHMCS\Database\Capsule::table('tblhosting')
            ->where('orderid', '=', $id)
            ->where('userid', '=', $clientid)->first();

        if ($order) {
            if ($order->username) {
                $username = $order->username;
            } else {
                $username = 'reseller_' . $order->userid . '_' . $order->orderid;
            }
            try {
                $data = array();
                $data['action'] = 'password';
                $data['cust_password'] = $_POST['password'];
                wlvpn_call_reseller_api("PUT", $data, $params['configoption2'] . "customers/" . $username,
                    $params['configoption1']);
                return 'success';
            } catch (Exception $e) {

                logModuleCall(
                    'wlvpn',
                    __FUNCTION__,
                    $params,
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                return $e->getMessage();
            }

        }
        return "Something went wrong";
    } else {
        return array(
            'templatefile' => 'reset_password',
            'breadcrumb' => array(
                'stepurl.php?action=this&var=that' => 'Custom Function',
            ),
            'vars' => array(),
        );
    }
}

/**
 * Function to handle different cURL requests based on the action user is performing
 *
 * @author Pradeesh Kumar P <pradeesh.kumar@stackpath.com>
 * @link https://jira.stackpath.net/browse/IPVDEV-879
 * @link https://jira.stackpath.net/browse/IPVDEV-894
 *
 * @param $action
 * @param $data
 * @param $uri
 * @param $api_key
 * @return string
 */
function wlvpn_call_reseller_api($action, $data, $uri, $api_key)
{


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json; charset=utf-8",
        "Accept:application/json, text/javascript, */*; q=0.01"
    ));
    curl_setopt($ch, CURLOPT_USERPWD, "api-key:" . $api_key);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    switch ($action) {
        case "POST":

            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "PUT":

            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            break;
        CASE "DELETE":

            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            break;
        case "GET":
        default:

            $uri .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $uri);

            break;
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return = curl_exec($ch);

    curl_close($ch);

    return $return;
}
