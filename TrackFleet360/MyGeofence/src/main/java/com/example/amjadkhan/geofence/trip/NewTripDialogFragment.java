package com.example.amjadkhan.geofence.trip;

import android.app.Dialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.DialogFragment;
import android.support.v7.app.AlertDialog;
import android.text.TextUtils;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.Toast;

import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.utils.CustomDateTimePicker;
import com.example.amjadkhan.geofence.utils.MyApp;
import com.google.android.gms.common.GooglePlayServicesNotAvailableException;
import com.google.android.gms.common.GooglePlayServicesRepairableException;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.ui.PlacePicker;
import com.jaredrummler.materialspinner.MaterialSpinner;

import java.util.Calendar;
import java.util.Date;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

public class NewTripDialogFragment extends DialogFragment implements NewTripDialogPresenter.OnNewTripAddListener {

    private static final String TAG = "NewTripDialogFragment";
    private static final int PLACE_PICKER_SRC_REQUEST_CODE = 100;
    private static final int PLACE_PICKER_DEST_REQUEST_CODE = 101;

     @BindView(R.id.et_dialog_trip_name)
    EditText tripName;
    @BindView(R.id.et_dialog_source_address)
    EditText tripSrcAdres;
    @BindView(R.id.et_dialog_dest_address)
    EditText tripDestAdres;
    @BindView(R.id.ib_dismiss_trip_dialog)
    ImageButton dismiss_dialog_btn;
    @BindView(R.id.et_dialog_pickup_time)
    EditText tripPickupTime;
    @BindView(R.id.spinner_dilaog_geofence)
    MaterialSpinner tripGeofenceSpinner;
    @BindView(R.id.spinner_dialog_poi)
    MaterialSpinner tripPoiSpinner;
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
    CustomDateTimePicker custom;
    private Place source;
    private Place destination;
    @BindView(R.id.et_dialog_driver)
    AutoCompleteTextView autoCompleteTextView;


    UpdateTripDialogFragment.TripUpdateListener listener;

    public void setTripAddedListener(UpdateTripDialogFragment.TripUpdateListener listener){
        this.listener = listener;
    }


    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View rootView = inflater.inflate(R.layout.dialog_new_trip,container,false);
        ButterKnife.bind(this,rootView);
        Log.d(TAG, "onCreateView: ");
        autoCompleteTextView.setAdapter(new ArrayAdapter<String>(getActivity(),android.R.layout.simple_list_item_1,getActivity().getResources().getStringArray(R.array.driver_name)));

        return rootView;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setStyle(DialogFragment.STYLE_NORMAL,R.style.ThemeOverlay_AppCompat_Dialog);
        setCancelable(false);

    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

          custom = new CustomDateTimePicker(getActivity(),
                new CustomDateTimePicker.ICustomDateTimeListener() {

                    @Override
                    public void onSet(Dialog dialog, Calendar calendarSelected,
                                      Date dateSelected, int year, String monthFullName,
                                      String monthShortName, int monthNumber, int date,
                                      String weekDayFullName, String weekDayShortName,
                                      int hour24, int hour12, int min, int sec,
                                      String AM_PM) {

                        tripPickupTime.setText(hour12+":"+min +" "+ AM_PM +",  "+ weekDayShortName + ", "+ date + " "+ monthShortName);


                    }

                    @Override
                    public void onCancel() {

                    }
                });


    }

    @Override
    public void onResume() {
        super.onResume();
        int width = getResources().getDimensionPixelSize(R.dimen.width_trip_dialog);
        int height = getResources().getDimensionPixelSize(R.dimen.height_trip_dialog);


        Log.d(TAG, "onResume: width :" + width + " height: "+ height);
//        getDialog().getWindow().setLayout(width,height);

    }




    @OnClick({R.id.btn_dialog_add_trip,R.id.btn_dialog_reset,R.id.ib_src_location,R.id.ib_dest_location,R.id.ib_clock,R.id.ib_dismiss_trip_dialog})
    public void onClick(View view) {

        switch (view.getId()) {
            case R.id.btn_dialog_reset :
                resetFields();
                break;

            case R.id.btn_dialog_add_trip:
                Log.d(TAG, "onClick: ");
                addNewTrip();
                break;

            case R.id.ib_src_location:
                try {
                    Intent intent = new PlacePicker.IntentBuilder().build(getActivity());
                    startActivityForResult(intent, PLACE_PICKER_SRC_REQUEST_CODE);
                } catch (GooglePlayServicesRepairableException e) {
                    e.printStackTrace();
                } catch (GooglePlayServicesNotAvailableException e) {
                    e.printStackTrace();
                }
               break;


            case R.id.ib_dest_location:
                try {
                    Intent intent = new PlacePicker.IntentBuilder().build(getActivity());
                    startActivityForResult(intent, PLACE_PICKER_DEST_REQUEST_CODE);
                } catch (GooglePlayServicesRepairableException e) {
                    e.printStackTrace();
                } catch (GooglePlayServicesNotAvailableException e) {
                    e.printStackTrace();
                }
                break;

            case R.id.ib_clock:
                custom.showDialog();
                break;

            case R.id.ib_dismiss_trip_dialog:
                dismiss();
                break;


        }

    }

    private void addNewTrip() {
        if (TextUtils.isEmpty(tripName.getText().toString())) {
            tripName.setError("Trip must have a name");
            Log.d(TAG, "updateTripDetail: tripname empty");
            return;
        }

        if (TextUtils.isEmpty(tripSrcAdres.getText().toString())) {
            tripSrcAdres.setError("Trip must have source address");
            Log.d(TAG, "updateTripDetail: trip src adrs empty");

            return;
        }

        if (TextUtils.isEmpty(tripDestAdres.getText().toString())) {
            tripDestAdres.setError("Trip must have destination address");
            Log.d(TAG, "updateTripDetail: trip dest adrs empty");

            return;
        }

        if (TextUtils.isEmpty(tripPickupTime.getText().toString())) {
            tripPickupTime.setError("Please provide trip pick up time");
            Log.d(TAG, "updateTripDetail: trip pick up time empty");

            return;
        }

       if (TextUtils.isEmpty(autoCompleteTextView.getText().toString())) {
            autoCompleteTextView.setError("Please assign a driver");

             return;
        }



//        NewTripDialogPresenter newTripDialogPresenter = new NewTripDialogPresenter(this);
//        Trip trip1 = new Trip(tripName.getText().toString(),source.getAddress().toString(),destination.getAddress().toString(),tripPickupTime.getText().toString(),autoCompleteTextView.getText().toString());



//        newTripDialogPresenter.addNewTrip(trip1);



        Log.d(TAG, "updateTripDetail: ");

    }

    private void showNetworkErrorDialog() {
        Log.d(TAG, "onClick: Not connected");
        AlertDialog alertDialog = new AlertDialog.Builder(getActivity()).setTitle("Info").
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

    public void resetFields(){

        tripName.getText().clear();
        tripSrcAdres.getText().clear();
        tripDestAdres.getText().clear();
        tripPickupTime.getText().clear();
    }

    @Override
    public void onTripAddSuccess(Trip trip) {
        Log.d(TAG, "onTripAddSuccess: ");
        dismiss();
    }

    @Override
    public void onTripAddFailed() {
        Toast.makeText(getActivity(), "Error creating new trip", Toast.LENGTH_SHORT).show();
     }
}
