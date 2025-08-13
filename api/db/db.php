<?php
    class DBConnection
    {
        public static function connect() {
//            $servername = "195.179.239.102";
//            $username   = "u234488260_fortis_dev";
//            $database   = "u234488260_fortis_dev";
//            $password   = "y5Q*|l9xnX0";

             $servername = "31.97.27.221";
             $username   = "u234488260_fortis";
             $database   = "u234488260_fortis";
             $password   = "y5Q*|l9xnX0";
            return new mysqli($servername, $username, $password, $database);
        }
    }