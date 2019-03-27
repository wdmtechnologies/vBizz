package com.example.amjadkhan.geofence.utils;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.os.Bundle;
import android.util.Log;
import android.widget.Toast;

import com.example.amjadkhan.geofence.utils.MyApp;

public class NetworkStateReceiver extends BroadcastReceiver {

    public static final String ACTION_CONNECTIVITY_CHANGE = "android.net.conn.CONNECTIVITY_CHANGE";

    public static final String TAG = "NetworkStateReceiver";
    @Override
    public void onReceive(Context context, Intent intent) {
         if (intent.getAction().equals(ACTION_CONNECTIVITY_CHANGE)) {


        }

    }


}
