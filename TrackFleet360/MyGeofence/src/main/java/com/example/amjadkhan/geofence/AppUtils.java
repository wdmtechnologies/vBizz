package com.example.amjadkhan.geofence;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.support.v7.app.AlertDialog;
import android.util.Log;

import static android.content.Context.CONNECTIVITY_SERVICE;

public  class AppUtils {
    public static final String TAG = "AppUtils";
    public static final int PLACE_PICKER_SRC_REQUEST_CODE = 100;
    public static final int PLACE_PICKER_DEST_REQUEST_CODE = 101;


    public static void showNetworkErrorDialog(final Context context){
        Log.d(TAG, "showNetworkErrorDialog: ");
        AlertDialog alertDialog = new AlertDialog.Builder(context).setTitle("Info").
                setIcon(R.drawable.ic_error_outline_black_24dp).
                setMessage("Internet not available, Cross check your internet connectivity and try again")
                .setPositiveButton("Settings", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        context.startActivity(new Intent(android.provider.Settings.ACTION_SETTINGS));

                    }
                }).setNegativeButton("Close", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {

                    }
                }).create();
//        alertDialog.getWindow().getAttributes().windowAnimations = R.style.PauseDialogAnimation;
        alertDialog.show();
    }


}
