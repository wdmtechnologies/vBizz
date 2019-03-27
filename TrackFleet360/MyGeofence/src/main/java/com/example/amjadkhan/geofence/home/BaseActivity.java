package com.example.amjadkhan.geofence.home;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;

import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.dagger.AppModule;
import com.example.amjadkhan.geofence.dagger.DaggerComponent;
import com.example.amjadkhan.geofence.dagger.NetModule;

public abstract class BaseActivity extends AppCompatActivity {
    private static final String TAG = "BaseActivity";
    BaseActivity activity;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.d(TAG, "onCreate: ");
        activity = this;
        setContentView(getLayoutRes());
        initViews();
    }

    public  boolean isNetworkAvailable(Context context) {
        ConnectivityManager cm = (ConnectivityManager) context.getSystemService(CONNECTIVITY_SERVICE);
        NetworkInfo activeNetworkInfo = cm.getActiveNetworkInfo();

        boolean connected = activeNetworkInfo != null && activeNetworkInfo.isConnectedOrConnecting() && activeNetworkInfo.isAvailable();
        Log.d(TAG, "isNetworkAvailable: "+ connected);
        return connected;
    }


    protected abstract int getLayoutRes();
    protected abstract void initViews();

    public DaggerComponent.Builder getDaggerBuilder(String baseUrl){
        return DaggerComponent.builder().appModule(new AppModule(this)).netModule(new NetModule(baseUrl));

    }



    public void showNetworkErrorDialog() {
        Log.d(TAG, "onClick: Not connected");
        AlertDialog alertDialog = new AlertDialog.Builder(this).
                setIcon(R.drawable.ic_error_outline_black_24dp).
                setMessage("Internet not available, Cross check your internet connectivity and try again")
                .setPositiveButton("Settings", (dialog, which) -> startActivity(new Intent(android.provider.Settings.ACTION_SETTINGS))).setNegativeButton("Close", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                    }
                }).create();
        alertDialog.getWindow().getAttributes().windowAnimations = R.style.PauseDialogAnimation;
        alertDialog.show();
    }

}
