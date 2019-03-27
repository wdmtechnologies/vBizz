package com.example.amjadkhan.geofence.trip;

import android.app.ActionBar;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.TimePickerDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.TextUtils;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.ProgressBar;
import android.widget.TimePicker;
import android.widget.Toast;

import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.account.Api;
import com.example.amjadkhan.geofence.account.Driver;
import com.example.amjadkhan.geofence.account.DriverListPresenter;
import com.example.amjadkhan.geofence.account.DriverView;
import com.example.amjadkhan.geofence.home.BaseActivity;
import com.example.amjadkhan.geofence.utils.MyApp;
import com.example.amjadkhan.geofence.utils.Session;
import com.example.amjadkhan.geofence.utils.TripServiceApi;
import com.google.android.gms.common.GooglePlayServicesNotAvailableException;
import com.google.android.gms.common.GooglePlayServicesRepairableException;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.ui.PlacePicker;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import javax.inject.Inject;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import retrofit2.Retrofit;

import static com.example.amjadkhan.geofence.AppUtils.PLACE_PICKER_DEST_REQUEST_CODE;
import static com.example.amjadkhan.geofence.AppUtils.PLACE_PICKER_SRC_REQUEST_CODE;

public class AddNewTripActivity extends BaseActivity implements DriverView,NewTripActivityView {

    private static final String TAG = "AddNewTripActivity";


    @Inject
    Session session;

    @BindView(R.id.et_dialog_trip_name)
    EditText tripName;
    @BindView(R.id.et_dialog_source_address)
    EditText tripSrcAdres;
    @BindView(R.id.et_dialog_dest_address)
    EditText tripDestAdres;
    @BindView(R.id.et_dialog_pickup_time)
    EditText tripPickupTime;
    @BindView(R.id.et_geofence1)
    EditText tripGeofence1;

    @BindView(R.id.spinner_dialog_poi)
    EditText tripPoi;
    @BindView(R.id.btn_dialog_add_trip)
    Button tripAddBtn;
    @BindView(R.id.btn_dialog_reset)
    Button tripResetBtn;
    @BindView(R.id.ib_src_location)
    ImageButton srcAdrsBtn;
    @BindView(R.id.ib_dest_location)
    ImageButton destAdrsBtn;
    @BindView(R.id.ib_clock)
    ImageButton pickup_time_img_btn;
     private Place source;
    private Place destination;
    @BindView(R.id.et_dialog_driver)
    AutoCompleteTextView driver_name;
    @BindView(R.id.toolbar_new_trip)
    Toolbar toolbar;
    @BindView(R.id.pb)
    ProgressBar pb;
    Calendar date;
    NewTripActivityPresenter presenter;
    List<Employee> employeeList;
    List<String> driverNames = new ArrayList<>();
    @Inject
    Retrofit retrofit;
    String empId ;
     List<Employee> idleDrivers = new ArrayList<>();
     String adminId;
     Double  srcLat;
    Double srcLng;



    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.d(TAG, "onCreate: ");
        getDaggerBuilder(TripServiceApi.BASE_URL).build().inject(this);
        presenter = new NewTripActivityPresenter(this,retrofit);
        fetchEmployees();

    }


    @Override
    protected void initViews() {
        ButterKnife.bind(this);

        setSupportActionBar(toolbar);
        android.support.v7.app.ActionBar actionBar = getSupportActionBar();
         actionBar.setDisplayOptions(android.support.v7.app.ActionBar.DISPLAY_HOME_AS_UP);
         actionBar.setDisplayHomeAsUpEnabled(true);
         actionBar.setDisplayShowTitleEnabled(true);
    }


//    private void initView() {
//        //setup toolbar
//        setSupportActionBar(toolbar);
//        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
//        getSupportActionBar().setDisplayShowHomeEnabled(true);
//
//        driver_name.setAdapter(new ArrayAdapter<String>(this,android.R.layout.simple_list_item_1,driverNames));
//        driver_name.setDropDownHeight(300);
//        driver_name.setThreshold(1);
//        driver_name.setDropDownAnchor(R.id.et_dialog_driver);
//
//        driver_name.setOnItemClickListener((parent, view, position, id) -> {
//            if (driverList != null) {
//
//                for (com.example.amjadkhan.geofence.Driver driver: idleDrivers) {
//                    if (driver.geteName().equals(driver_name.getText().toString())) {
//                        empId = driver.geteId();
//                    }
//                }
//
//                if (empId == null) {
//                    Toast.makeText(AddNewTripActivity.this, "Please select a driver", Toast.LENGTH_SHORT).show();
//                }
//
//            }
//
//            Log.d(TAG, "onItemClick: position"+ position);
//            Log.d(TAG, "onItemClick: "+empId);
//
//        });
//
//    }


    private void fetchEmployees() {
        Log.d(TAG, "fetchEmployees: ");
         presenter.fetchEmployees(session.getAdminId());

    }

    @Override
    public void onEmployeeFetchedSuccess(List<Employee> employeeList) {
        Log.d(TAG, "onEmployeeFetchedSuccess: "+ employeeList);

        this.employeeList = employeeList;

    }
    @Override
    public void onEmployeeFetchFailed() {
        Log.d(TAG, "onEmployeeFetchFailed: ");
        Toast.makeText(this, "Employee fetch failed", Toast.LENGTH_SHORT).show();
    }


    private void addNewTrip() {
        Log.d(TAG, "addNewTrip: ");
        if (TextUtils.isEmpty(tripName.getText().toString())) {
            tripName.setError("Trip must have a name");
             return;
        }
        if (TextUtils.isEmpty(tripSrcAdres.getText().toString())) {
            tripSrcAdres.setError("Trip must have source address");
            return;
        }
        if (TextUtils.isEmpty(tripDestAdres.getText().toString())) {
            tripDestAdres.setError("Trip must have destination address");

            return;
        }
        if (TextUtils.isEmpty(tripPickupTime.getText().toString())) {
            tripPickupTime.setError("Please provide trip pick up time");

            return;
        }

        if (TextUtils.isEmpty(driver_name.getText().toString())) {
            driver_name.setError("Please assign a driver");
            return;
        }

        if (empId == null) {
            Toast.makeText(this, "Please select a valid driver", Toast.LENGTH_SHORT).show();
            return;
        }


         if (!isNetworkAvailable(this)) {
              showNetworkErrorDialog();
              return;

        }

        presenter = new NewTripActivityPresenter(this,retrofit);
        Log.d(TAG, "addNewTrip: 190 "+empId);
        Trip trip = new Trip(tripName.getText().toString()
                ,tripSrcAdres.getText().toString()
                ,tripDestAdres.getText().toString()
                ,tripPickupTime.getText().toString()
                ,empId
                ,
                adminId,srcLat,srcLng);

        pb.setVisibility(View.VISIBLE);
        tripAddBtn.setVisibility(View.GONE);
         tripResetBtn.setVisibility(View.GONE);

        presenter.addNewTrip(trip);

        Log.d(TAG, "updateTripDetail: ");

    }










    @Override
    public void onTripAddSuccess(String tripId) {
        pb.setVisibility(View.GONE);
        Toast.makeText(this, "Trip id: "+ tripId, Toast.LENGTH_SHORT).show();
        Log.d(TAG, "onTripAddSuccess: ");
        finish();
    }

    @Override
    public void onTripAddFailed() {
        pb.setVisibility(View.GONE);
        tripResetBtn.setVisibility(View.VISIBLE);
        tripAddBtn.setVisibility(View.VISIBLE);
        Toast.makeText(this, "Error creating new trip,Cross check your internet connection", Toast.LENGTH_SHORT).show();
    }





    @Override
    protected int getLayoutRes() {
        return R.layout.activity_add_new_trip;
    }

    private void showDateTimeDialog() {
        final   Calendar date;

        final Calendar currentDate = Calendar.getInstance();
        date = Calendar.getInstance();
        new DatePickerDialog(AddNewTripActivity.this, new DatePickerDialog.OnDateSetListener() {
            @Override
            public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                date.set(year, monthOfYear, dayOfMonth);
                new TimePickerDialog(AddNewTripActivity.this, new TimePickerDialog.OnTimeSetListener() {
                    @Override
                    public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
                        date.set(Calendar.HOUR_OF_DAY, hourOfDay);
                        date.set(Calendar.MINUTE, minute);
                        Toast.makeText(AddNewTripActivity.this, date.getTime().toString(), Toast.LENGTH_SHORT).show();
                        Log.v(TAG, "The choosen one " + date.getTime());
                        android.text.format.DateFormat df = new android.text.format.DateFormat();
                        CharSequence format = df.format("hh:mm a , dd/MM/yyyy", date);
                        tripPickupTime.setText(format);

                    }
                }, currentDate.get(Calendar.HOUR_OF_DAY), currentDate.get(Calendar.MINUTE), false).show();
            }
        }, currentDate.get(Calendar.YEAR), currentDate.get(Calendar.MONTH), currentDate.get(Calendar.DATE)).show();
    }




    private void chooseLocation(int PLACE_PICKER_REQUEST_CODE) {
        try {
            Intent intent = new PlacePicker.IntentBuilder().build(this);
            startActivityForResult(intent, PLACE_PICKER_REQUEST_CODE);
        } catch (GooglePlayServicesRepairableException e) {
            e.printStackTrace();
        } catch (GooglePlayServicesNotAvailableException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        Log.d(TAG, "onActivityResult: ");

        if (resultCode == Activity.RESULT_OK && data != null) {
            Log.d(TAG, "onActivityResult: 355");
            if (data != null) {
                Log.d(TAG, "onActivityResult: 357");
                Place place = PlacePicker.getPlace(this,data);
                if (requestCode == PLACE_PICKER_SRC_REQUEST_CODE) {
                    Log.d(TAG, "onActivityResult: 360");
                    tripSrcAdres.setText(place.getAddress());
                    srcLat = place.getLatLng().latitude;
                    srcLng = place.getLatLng().longitude;

                    Log.d(TAG, "onActivityResult: latng "+ srcLng + srcLat);
                }
                else if (requestCode == PLACE_PICKER_DEST_REQUEST_CODE){
                    tripDestAdres.setText(place.getAddress());
                    return;
                }

            }
            return;


        }

    }


    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        Log.d(TAG, "onOptionsItemSelected: ");
        switch (item.getItemId()) {
            case android.R.id.home:
                Log.d(TAG, "onOptionsItemSelected: ");
                finish();
                break;

        }
        return super.onOptionsItemSelected(item);
    }

    @OnClick({R.id.btn_dialog_add_trip,R.id.btn_dialog_reset,R.id.ib_src_location,R.id.ib_dest_location,R.id.ib_clock})
    public void onClick(View view) {
        Log.d(TAG, "onClick: ");
        switch (view.getId()) {
            case R.id.btn_dialog_reset :
                resetFields();
                break;

            case R.id.btn_dialog_add_trip:
                Log.d(TAG, "onClick: ");
                addNewTrip();
                break;

            case R.id.ib_src_location:
                chooseLocation(PLACE_PICKER_SRC_REQUEST_CODE);
                break;


            case R.id.ib_dest_location:
                chooseLocation(PLACE_PICKER_DEST_REQUEST_CODE);
                break;

            case R.id.ib_clock:
                showDateTimeDialog();
                break;

        }

    }


    public void resetFields(){

        tripName.getText().clear();
        tripSrcAdres.getText().clear();
        tripDestAdres.getText().clear();
        tripPickupTime.getText().clear();
        driver_name.getText().clear();
    }


}
