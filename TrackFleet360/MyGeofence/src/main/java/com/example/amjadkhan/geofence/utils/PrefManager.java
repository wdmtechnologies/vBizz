package com.example.amjadkhan.geofence.utils;

import android.app.Activity;
import android.content.SharedPreferences;
import android.util.Log;

public class PrefManager {

    //Constants
    private static final String APP_LAUNCHED_COUNT = "app_used_count";
    private static final int PRIVATE_MODE  = 0;
    private static final String PREF_NAME = "app_pref";
    private static final String IS_FIRST_TIME_LAUCNH = "first_time_launch";
    private static final String TAG = "PrefManager";

    //Context
    private Activity activity;

    //Data
    private SharedPreferences preferences;
    private SharedPreferences.Editor editor;

    public PrefManager(Activity activity) {
        this.activity = activity;
        preferences = activity.getSharedPreferences(PREF_NAME,PRIVATE_MODE);
        editor = preferences.edit();

    }

    public boolean isFirstLaunch() {
        /**
         * Returns default value if app is launched first time, coz key-value is not set yet
          */
       return  preferences.getBoolean(IS_FIRST_TIME_LAUCNH,true);
    }


    public void setFirstLaunch(boolean firstLaunch){

         editor.putBoolean(IS_FIRST_TIME_LAUCNH, firstLaunch);
         editor.commit();
    }

    public void incrementAppLaunchedCount(){
        Log.d(TAG, "incrementAppLaunchedCount: ");
         int appLaunchedCurrentCount = getAppLaunchedCount() + 1;
         editor.putInt(APP_LAUNCHED_COUNT,appLaunchedCurrentCount);
    }

    public int getAppLaunchedCount(){
        return preferences.getInt(APP_LAUNCHED_COUNT,0);
    }



}
