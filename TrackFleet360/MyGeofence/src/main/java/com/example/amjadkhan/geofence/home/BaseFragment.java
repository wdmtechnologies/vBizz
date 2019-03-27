package com.example.amjadkhan.geofence.home;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.app.AlertDialog;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.account.Api;
import com.example.amjadkhan.geofence.dagger.AppModule;
import com.example.amjadkhan.geofence.dagger.DaggerComponent;
import com.example.amjadkhan.geofence.dagger.NetModule;

import butterknife.ButterKnife;

import static android.content.Context.CONNECTIVITY_SERVICE;

public abstract class BaseFragment extends Fragment {

    private static final String TAG = "BaseFragment";

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(getLayoutRes(),container,false);
        ButterKnife.bind(this,view);
        return view;
    }



    protected abstract int getLayoutRes();


    public DaggerComponent.Builder getDaggerBuilder(String baseUrl){
        return DaggerComponent.builder().appModule(new AppModule(getActivity())).netModule(new NetModule(baseUrl));

    }

    public boolean isNetworkAvailable(Context context) {
        ConnectivityManager cm = (ConnectivityManager) context.getSystemService(CONNECTIVITY_SERVICE);
        NetworkInfo activeNetworkInfo = cm.getActiveNetworkInfo();

        boolean connected = activeNetworkInfo != null && activeNetworkInfo.isConnectedOrConnecting() && activeNetworkInfo.isAvailable();
        Log.d(TAG, "isNetworkAvailable: "+ connected);
        return connected;
    }

    public void showNetworkErrorDialog() {

        //hide bottom  trip bar


        Log.d(TAG, "onClick: Not connected");
      AlertDialog  alertDialog = new AlertDialog.Builder(getActivity()).setTitle("Info").
                setIcon(R.drawable.ic_error_outline_black_24dp).
                setMessage("Internet not available, Cross check your internet connectivity and try again")
                .setPositiveButton("Settings", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        startActivity(new Intent(android.provider.Settings.ACTION_SETTINGS));

                    }
                }).setNegativeButton("Close", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                    }
                }).create();

        alertDialog.show();
    }

}
