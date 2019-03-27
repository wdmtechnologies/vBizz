package com.example.amjadkhan.geofence.trip;

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
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.ImageButton;
import android.widget.TimePicker;
import android.widget.Toast;

import com.example.amjadkhan.geofence.AppUtils;
import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.account.Api;
import com.example.amjadkhan.geofence.account.DriverListPresenter;
import com.example.amjadkhan.geofence.account.DriverView;
import com.example.amjadkhan.geofence.utils.MyApp;
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

public class UpdateTripActivity extends AppCompatActivity implements DriverView,NewTripDialogPresenter.OnNewTripAddListener {

    private static final String TAG = "UpdateTripActivity";

    @BindView(R.id.et_dialog_trip_name)
    EditText tripName;
    @BindView(R.id.et_dialog_source_address)
    EditText tripSrcAdres;
    @BindView(R.id.et_dialog_dest_address)
    EditText tripDestAdres;
    @BindView(R.id.et_dialog_pickup_time)
    EditText tripPickupTime;
    @BindView(R.id.et_geofence1)
    EditText tripGeofence;
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
    Calendar date;
    DriverListPresenter driverListPresenter;
    List<Employee> driverList;
    List<String> driverNames = new ArrayList<>();
    @Inject
    Retrofit retrofit;
    String empId = null;
    Trip currentTrip;
    List<Employee> idleDrivers = new ArrayList<>();
    int currentDriverId = -1;


    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        loadDrivers();
        setContentView(R.layout.activity_add_new_trip);

        ButterKnife.bind(this);
        fillFields();

        tripAddBtn.setText("Update");
        Intent intent = getIntent();

        initView();
    }



    private void initView() {
        //setup toolbar
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        setupDriverSpinner();


    }

    private void setupDriverSpinner() {

        driver_name.setAdapter(new ArrayAdapter<String>(this,android.R.layout.simple_list_item_1,driverNames));
        driver_name.setDropDownHeight(300);
        driver_name.setThreshold(1);
        driver_name.setDropDownAnchor(R.id.et_dialog_driver);

        driver_name.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
//                if (driverList != null) {
//
//                    for (com.example.amjadkhan.geofence.Driver driver: idleDrivers) {
//                        if (driver.geteName().equals(driver_name.getText().toString())) {
//                            empId = driver.geteId();
//                        }
//                    }
//
//                    if (empId == null) {
//                        Toast.makeText(UpdateTripActivity.this, "Please select a driver", Toast.LENGTH_SHORT).show();
//                    }
//
//                }
//
//                Log.d(TAG, "onItemClick: position"+ position);
//                Log.d(TAG, "onItemClick: "+empId);

            }
        });
        
        

    }

    private void updateTrip() {
        Log.d(TAG, "updateTrip: ");
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


        NewTripDialogPresenter newTripDialogPresenter = new NewTripDialogPresenter(this,retrofit);
        if (currentTrip != null) {
            Log.d(TAG, "updateTrip: 177");
            currentTrip.setName(tripName.getText().toString());
            currentTrip.setSourceAdrs(tripSrcAdres.getText().toString());
            currentTrip.setDestAdrs(tripDestAdres.getText().toString());
            currentTrip.setPickupTime(tripPickupTime.getText().toString());
             if (empId == null) {
                empId = currentTrip.getEmp_id();
             }
            currentTrip.setEmp_id(empId);
            Log.d(TAG, "updateTrip: "+empId);


        }
//          if (!MyApp.isNetworkAvailable(this)) {
//            showNetworkErrorDialog();
//            return;
//
//        }

        newTripDialogPresenter.updateTrip(currentTrip);

        Log.d(TAG, "updateTripDetail: ");

    }

    private void showNetworkErrorDialog() {
        Log.d(TAG, "onClick: Not connected");
        AlertDialog  alertDialog = new AlertDialog.Builder(this).
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

                    }
                }).create();
        alertDialog.getWindow().getAttributes().windowAnimations = R.style.PauseDialogAnimation;
        alertDialog.show();
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
                updateTrip();
                break;

            case R.id.ib_src_location:
                chooseLocation(AppUtils.PLACE_PICKER_SRC_REQUEST_CODE);
                break;


            case R.id.ib_dest_location:
                chooseLocation(AppUtils.PLACE_PICKER_DEST_REQUEST_CODE);
                break;

            case R.id.ib_clock:
                showDateTimeDialog();
                 break;

        }

    }

    private void showDateTimeDialog() {
      final   Calendar date;

            final Calendar currentDate = Calendar.getInstance();
            date = Calendar.getInstance();
            new DatePickerDialog(UpdateTripActivity.this, new DatePickerDialog.OnDateSetListener() {
                @Override
                public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                    date.set(year, monthOfYear, dayOfMonth);
                    new TimePickerDialog(UpdateTripActivity.this, new TimePickerDialog.OnTimeSetListener() {
                        @Override
                        public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
                            date.set(Calendar.HOUR_OF_DAY, hourOfDay);
                            date.set(Calendar.MINUTE, minute);
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

             if (resultCode == Activity.RESULT_OK) {
                if (data != null) {
                    Place place = PlacePicker.getPlace(this,data);
                     if (requestCode == AppUtils.PLACE_PICKER_SRC_REQUEST_CODE) {
                        tripSrcAdres.setText(place.getAddress());
                    }
                    else{
                        tripDestAdres.setText(place.getAddress());
                    }
                    Log.d(TAG, "onActivityResult: Src "+ place.getAddress());
                    return;

                }
            }

        }


    public void resetFields(){

        tripName.getText().clear();
        tripSrcAdres.getText().clear();
        tripDestAdres.getText().clear();
        tripPickupTime.getText().clear();
        driver_name.getText().clear();
    }

    private void loadDrivers() {
        Log.d(TAG, "loadDrivers: ");
        MyApp.getDaggerComponent(Api.BASE_URL).inject(this);
        driverListPresenter = new DriverListPresenter(this,retrofit);
//        driverListPresenter.loadDrivers();

    }


    @Override
    public void onTripAddSuccess(Trip trip) {
        Log.d(TAG, "onTripAddSuccess: ");
        finish();
    }

    @Override
    public void onTripAddFailed() {
        Toast.makeText(this, "Error creating new trip", Toast.LENGTH_SHORT).show();
    }


    //Update mode
    private void fillFields() {
        Intent intent = getIntent();
        tripName.setText(intent.getStringExtra("trip_name"));
        tripSrcAdres.setText(intent.getStringExtra("src_address"));
        tripDestAdres.setText(intent.getStringExtra("dest_address"));
         tripPickupTime.setText(intent.getStringExtra("pickup_time"));
         tripPickupTime.setText(intent.getStringExtra("driver_name"));

//        driver_name.addTextChangedListener(new TextWatcher() {
//            @Override
//            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
//
//            }
//
//            @Override
//            public void onTextChanged(CharSequence s, int start, int before, int count) {
//                if (driverList == null) {
//                    Log.d(TAG, "onTextChanged: ");
//                    loadDrivers();
//                }
//            }
//
//            @Override
//            public void afterTextChanged(Editable s) {
//
//            }
//        });

    }

    @Override
    public void onEmployeeFetchedSuccess(List<Employee> employees) {

    }

    @Override
    public void onEmployeeFetchFailed() {

    }

//    @Override
//    public void onDriversFetchedSuccess(List<Employee> drivers) {
//        if (drivers != null && !drivers.isEmpty()) {
//            driverList = drivers;

//            if (driverList != null) {
//                for (com.example.amjadkhan.geofence.Driver driver: driverList) {
//                    Log.d(TAG, "onDriversListFetched: "+ driver.getTripAlloted());
//                    if (driver.getTripAlloted().equals("0")) {
//                        idleDrivers.add(driver);
//                    }
//
//
//                    if (driver.geteId().equals(currentTrip.getEmp_id())) {
//                        driver_name.setText(driver.geteName());
//
//                    }
//                }
//                Log.d(TAG, "onDriversListFetched: Current idle drivers: "+idleDrivers.size());
//            }
//
//
//            for (com.example.amjadkhan.geofence.Driver driver : idleDrivers) {
//                driverNames.add(driver.geteName());
//            }
//            Log.d(TAG, "onDriversListFetched: driver names size: "+driverNames.size());
//            setupDriverSpinner();

        }



//    @Override
//    public void onDriversFetchFailed() {
//
//    }

//    @Override
//    public void onEmployeeFetchedSuccess(List<Employee> employees) {
//
//    }
//
//    @Override
//    public void onEmployeeFetchFailed() {
//
//    }


//    public class SpinnerAdapter extends BaseAdapter implements Filterable {
//
//        @Override
//        public int getCount() {
//            return 10;
//        }
//
//        @Override
//        public Object getItem(int position) {
//            return null;
//        }
//
//        @Override
//        public long getItemId(int position) {
//            return 0;
//        }
//
//        @Override
//        public View getView(int position, View convertView, ViewGroup parent) {
//            if (convertView == null) {
//                 convertView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_driver_drop_down, null, false);
//            }
//            return convertView;
//        }
//
//        @Override
//        public Filter getFilter() {
//            return null;
//        }
//    }
    


