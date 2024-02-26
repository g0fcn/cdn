<?php
namespace Bee\Beebox\Utils;

class LicenseCore {
    // get key 
    /**
     * @param $target 
     */
    public static function getLicenseCode() {
        $config = get_option('beebox_license_code', '');
        return $config;
    }

    /**
     * @param $target 
     */
    public static function updateLicenseCode($code) {
        update_option('beebox_license_code', $code);
    }

    public static function updateStartDate($date) {
        if(get_option('beebox_license_startdate', null)) {
        } else {
            update_option('beebox_license_startdate', $date);
        }
        return get_option('beebox_license_startdate', null);
    }

    public static function checkVerificationCode($code) {
        $pass = $code == 1024;
        $state = self::getVerificationState();
        if ($state) {
            $amount = self::getTrailAmount();
            if ($amount > 5) {
                update_option('beebox_license_trail_amount', 0);
            } else {
            }
        } else {
            if ($pass) {
                update_option('beebox_license_verification_state', $pass);
                update_option('beebox_license_trail_amount', 5);
            } else {
                update_option('beebox_license_trail_amount', 0);
            }
        }

        return array(
            'success' => $pass,
            'code' => $code,
            'amount' => self::getTrailAmount()
        );
    }

    public static function getTrailAmount() {
        return get_option('beebox_license_trail_amount', 0);
    }

    public static function updateTrailAmount($amount = 0) {
        if($amount > 5 || $amount < 0) {
            $amount = 0;
        }
        update_option('beebox_license_trail_amount', $amount);
    }

    public static function getVerificationState() {
        return  get_option('beebox_license_verification_state', false);
    }

    public static function getVerification() {
        $state = get_option('beebox_license_verification_state', false);
        $code = '';
        if ($state) {
            $code = 1024;
        }

        return array(
            'success' => $state,
            'code' => $code,
            'amount' => $state ? self::getTrailAmount() : 0
        );
    }
}