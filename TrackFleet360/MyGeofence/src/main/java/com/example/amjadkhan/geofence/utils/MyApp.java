package com.example.amjadkhan.geofence.utils;

import android.app.Application;
import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.util.Log;

import com.amitshekhar.DebugDB;
import com.example.amjadkhan.geofence.BaseView;
import com.example.amjadkhan.geofence.dagger.AppModule;
import com.example.amjadkhan.geofence.dagger.Component;
import com.example.amjadkhan.geofence.dagger.DaggerComponent;
import com.example.amjadkhan.geofence.dagger.NetModule;


public class MyApp extends Application {

     private static final String TAG = "MyApp";
     public static Context context;
      private static Application application;
      public static boolean isNetworkAvailable(Context context) {
          ConnectivityManager cm = (ConnectivityManager) context.getSystemService(CONNECTIVITY_SERVICE);
           NetworkInfo activeNetworkInfo = cm.getActiveNetworkInfo();

           boolean connected = activeNetworkInfo != null && activeNetworkInfo.isConnectedOrConnecting() && activeNetworkInfo.isAvailable();
          Log.d(TAG, "isNetworkAvailable: "+ connected);
          return connected;
      }

    @Override
    public void onCreate() {
        super.onCreate();
        context = getApplicationContext();

        DebugDB.initialize(context);
        Log.d(TAG, "onCreate: "+DebugDB.isServerRunning());
        Log.d(TAG, "onCreate: "+        DebugDB.getAddressLog());
     }


//    public static void updateAndroidSecurityProvider(Activity callingActivity) {
//        Log.d(TAG, "updateAndroidSecurityProvider: ");
//        try {
//            ProviderInstaller.installIfNeeded(callingActivity);
//        } catch (GooglePlayServicesRepairableException e) {
//            // Thrown when Google Play Services is not installed, up-to-date, or enabled
//            // Show dialog to allow users to install, update, or otherwise enable Google Play services.
//            GooglePlayServicesUtil.getErrorDialog(e.getConnectionStatusCode(), callingActivity, 0);
//            AlertDialog alertDialog = new AlertDialog.Builder(callingActivity).setMessage("Please update your GooglePlay Services").create();
//            alertDialog.show();
//        } catch (GooglePlayServicesNotAvailableException e) {
//            Log.e("SecurityException", "Google Play Services not available.");
//        }
//    }



    public static Component getDaggerComponent(String baseUrl){
        Component component = DaggerComponent.builder().appModule(new AppModule(context))
                .netModule(new NetModule(baseUrl)).build();
        return component;
    }
}
