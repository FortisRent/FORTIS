package org.fortis.service;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Db {
    private static final String DB_URL = "jdbc:mysql://195.179.239.102:3306/u234488260_fortis_dev";
	private static final String DB_USER = "u234488260_fortis_dev";
	private static final String DB_PASSWORD = "y5Q*|l9xnX0";

    public static Connection connect(){
       try {
            return DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
        } catch (SQLException e) {
            e.printStackTrace();
            return null;
        }
    }
}
