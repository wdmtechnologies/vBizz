package com.example.amjadkhan.geofence.home;

import android.Manifest;
import android.annotation.TargetApi;
import android.content.pm.PackageManager;
import android.os.Build;
import android.support.design.widget.BottomNavigationView;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.WindowManager;
import android.widget.Toast;

import com.example.amjadkhan.geofence.trip.TripsFragment;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.account.ProfileFragment;


import butterknife.BindView;
import butterknife.ButterKnife;

public class HomeActivity extends BaseActivity  {

     private static final String TAG = "HomeActivity";
    int   backPressedCount = 0;
    @BindView(R.id.bottom_nav_view)
    BottomNavigationView bottomNavigationView;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        this.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
         Log.d(TAG, "onCreate: 62");

          replaceFragment(MapFragment.newInstance());

    }

    @Override
    protected int getLayoutRes() {
        return R.layout.activity_home;
    }

    @Override
    protected void initViews() {
        Log.d(TAG, "initViews: ");
        /**
         * Injecting Views
         */
        ButterKnife.bind(this);

        /**
         * Setup Bottom Navigation
         */
        setupBottomNavView();

    }

    @Override
    protected void onResume() {
        super.onResume();
        Log.d(TAG, "onResume: ");

    }

    private void replaceFragment(Fragment fragment) {

        FragmentManager fm = getSupportFragmentManager() ;
        FragmentTransaction ft = fm.beginTransaction();
        if (fm.getBackStackEntryCount() >= 2) {
            ft.replace(R.id.fragment_container,fragment);
            ft.commit();
            return;

        }
        ft.replace(R.id.fragment_container,fragment).addToBackStack(fragment.getArguments().getString("name"));
        ft.commit();
    }


    @Override
    public void onBackPressed() {

        Log.d(TAG, "onBackPressed: ");
        //Check trips fragment has focus and disable the back button
      int count =   getSupportFragmentManager().getBackStackEntryCount();

        Log.d(TAG, "onBackPressed: "+count);
      if (count == 1){
          backPressedCount++;
          Toast.makeText(this, "Press again to exit", Toast.LENGTH_SHORT).show();
          if (backPressedCount == 2) {
              finish();
          }
      }
      else{
          bottomNavigationView.setSelectedItemId(R.id.ic_map);
          getSupportFragmentManager().popBackStack();

      }
      
    }



    private void setupBottomNavView() {
        Log.d(TAG, "setupBottomNavView: ");
        bottomNavigationView.setOnNavigationItemSelectedListener(menuItem -> {

            switch (menuItem.getItemId()) {

                case R.id.ic_map:
                    Log.d(TAG, "setupBottomNavView: 136");
                    replaceFragment(MapFragment.newInstance());
                    break;

                case R.id.ic_trip:
                    Log.d(TAG, "setupBottomNavView: 141");
                    replaceFragment(TripsFragment.newInstance());
                    break;

                case R.id.ic_account:
                    Log.d(TAG, "setupBottomNavView: 146");
                    replaceFragment(ProfileFragment.newInstance());
                    break;

            }
            return true;
        });

    }

}
