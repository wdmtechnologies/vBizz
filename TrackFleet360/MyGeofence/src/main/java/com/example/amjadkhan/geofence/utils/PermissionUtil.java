package com.example.amjadkhan.geofence.utils;

import android.Manifest;
import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.pm.PackageManager;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.app.ActivityCompat;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AlertDialog;
import android.util.Log;

public class PermissionUtil {
    private static final String TAG = "PermissionUtil";
    public static final int REQUEST_CODE = 100;
    Activity activity;
    String permission;
    boolean shouldShowPermissionRationale;

    public PermissionUtil(Activity context) {
        this.activity = context;
    }


    @RequiresApi(api = Build.VERSION_CODES.M)
   public boolean isPermissionGranted(String permission) {

        this.permission = permission;
        if (activity.checkSelfPermission(permission) != PackageManager.PERMISSION_GRANTED) {
            Log.d(TAG, "PermissionNotGranted: ");
            return shouldaskForPermission();
        }
        return true;
        //Permission Not yet granted
    }

    public boolean shouldaskForPermission() {
        Log.d(TAG, "askForPermission: ");
        /**
         * Check if permission rationale should be shown, returns false if user did  opted to not to ask again after first run
         * returns true if its first run or user just denied the permission and not check that checkbox yet
         */

        Log.d(TAG, "askForPermission: " + ActivityCompat.shouldShowRequestPermissionRationale(activity, Manifest.permission.READ_CONTACTS));
        if (ActivityCompat.shouldShowRequestPermissionRationale(activity, Manifest.permission.READ_CONTACTS)) {
            //Go ahead and ask again
            Log.d(TAG, "showRequestPermissionRationale: ");
            ActivityCompat.requestPermissions(activity, new String[]{Manifest.permission.READ_CONTACTS}, REQUEST_CODE);

        } else {
            return false;
         }
        /**
         * User not agreed on this permission, opted for never ask again
         */

     return false;
    }





}
