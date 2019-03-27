package com.example.amjadkhan.geofence.trip;

import android.app.Activity;
import android.app.Dialog;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.DialogFragment;
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
import android.widget.TextView;

import com.example.amjadkhan.geofence.R;
 import com.example.amjadkhan.geofence.utils.CustomDateTimePicker;
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

public class UpdateTripDialogFragment extends DialogFragment {

    private static final String TAG = "UpdateFragment";
    private static final int PLACE_PICKER_SRC_REQUEST_CODE = 100;
    private static final int PLACE_PICKER_DEST_REQUEST_CODE = 101;

    @BindView(R.id.trip_label)
    TextView tv_dialog_label;
    @BindView(R.id.et_dialog_trip_name)
    EditText trip_name;
    @BindView(R.id.et_dialog_source_address)
    EditText tripSrcAdrs;
    @BindView(R.id.et_dialog_dest_address)
    EditText tripDestAdrs;
    @BindView(R.id.et_dialog_pickup_time)
    EditText tripPickupTime;
    @BindView(R.id.spinner_dilaog_geofence)
    MaterialSpinner tripGeofenceSpinner;
    @BindView(R.id.spinner_dialog_poi)
    MaterialSpinner tripPoiSpinner;
    @BindView(R.id.btn_dialog_add_trip)
    Button tripUpdateBtn;
    @BindView(R.id.btn_dialog_reset)
    Button tripResetBtn;
    @BindView(R.id.ib_src_location)
    ImageButton srcAdrsBtn;
    @BindView(R.id.ib_dest_location)
    ImageButton destAdrsBtn;
    @BindView(R.id.ib_clock)
    ImageButton pickup_time_img_btn;
    @BindView(R.id.ib_dismiss_trip_dialog)
    ImageButton dismiss_dialog_btn;
    CustomDateTimePicker custom;
    @BindView(R.id.et_dialog_driver)
    AutoCompleteTextView autoCompleteTextView;
    private Place source;
    private Place destination;



    private TripUpdateListener listener;
    public void setTripUpdateListenr(TripUpdateListener  listener) {
         this.listener = listener;
    }

    public interface TripUpdateListener {

        void onTripUpdated(Trip trip);
        void onNewTripAdded(Trip trip);
    }
    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View rootView = inflater.inflate(R.layout.dialog_new_trip,container,false);
        ButterKnife.bind(this,rootView);
        Log.d(TAG, "onCreateView: ");
        return rootView;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setStyle(DialogFragment.STYLE_NORMAL,R.style.ThemeOverlay_AppCompat_Dialog);

    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        tv_dialog_label.setText("Update Trip");
        tripUpdateBtn.setText("Update");

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

        custom.setDate(Calendar.getInstance());
        autoCompleteTextView.setAdapter(new ArrayAdapter<String>(getActivity(),android.R.layout.simple_list_item_1,getActivity().getResources().getStringArray(R.array.driver_name)));


        Bundle bundle = getArguments();
        if (bundle != null) {

            trip_name.setText(bundle.getString("trip_name"));
            tripSrcAdrs.setText(bundle.getString("trip_src_address"));
            tripDestAdrs.setText(bundle.getString("trip_dest_address"));
            tripPickupTime.setText(bundle.getString("trip_picuptime"));
            autoCompleteTextView.setText(bundle.getString("trip_driver"));


        }

    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        Log.d(TAG, "onActivityResult: ");


        if (requestCode == PLACE_PICKER_SRC_REQUEST_CODE) {
            if (resultCode == Activity.RESULT_OK) {
                if (data != null) {
                    Place place = PlacePicker.getPlace(getActivity(), data);

                    tripSrcAdrs.setText(place.getAddress());
                    source = place;

                    Log.d(TAG, "onActivityResult: Src " + place.getAddress());
                    return;

                }
            }

        }

        if (requestCode == PLACE_PICKER_DEST_REQUEST_CODE) {
            Log.d(TAG, "onActivityResult: Dest");
            if (resultCode == Activity.RESULT_OK) {
                if (data != null) {
                    Place place = PlacePicker.getPlace(getActivity(), data);
                    tripDestAdrs.setText(place.getAddress());
                    destination = place;

                    Log.d(TAG, "onActivityResult: Src " + place.getAddress());

                }
            }
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        int width = getResources().getDimensionPixelSize(R.dimen.width_trip_dialog);
        int height = getResources().getDimensionPixelSize(R.dimen.height_trip_dialog);
//         getDialog().getWindow().setLayout(width,height);
     }

    @OnClick({R.id.ib_dismiss_trip_dialog,R.id.btn_dialog_add_trip,R.id.btn_dialog_reset,R.id.ib_src_location,R.id.ib_dest_location,R.id.ib_clock})
    public void onClick(View view) {

        switch (view.getId()) {
            case R.id.btn_dialog_reset :

                resetFields();
                break;

            case R.id.btn_dialog_add_trip:
                Log.d(TAG, "onClick: ");
                updateTripDetail();
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

    private void updateTripDetail() {
        Log.d(TAG, "updateTripDetail: ");
        if (TextUtils.isEmpty(trip_name.getText().toString())) {
            trip_name.setError("Trip must have a name");
            Log.d(TAG, "updateTripDetail: tripname empty");
            return;
        }

        Log.d(TAG, "updateTripDetail: 255");


        if (TextUtils.isEmpty(tripSrcAdrs.getText().toString())) {
            tripSrcAdrs.setError("Trip must have source address");
            Log.d(TAG, "updateTripDetail: trip src adrs empty");

            return;
        }

        Log.d(TAG, "updateTripDetail: 265");

        if (TextUtils.isEmpty(tripDestAdrs.getText().toString())) {
            tripDestAdrs.setError("Trip must have destination address");
            Log.d(TAG, "updateTripDetail: trip dest adrs empty");

            return;
        }

        Log.d(TAG, "updateTripDetail: 274 ");

        if (TextUtils.isEmpty(tripPickupTime.getText().toString())) {
            tripPickupTime.setError("Please provide trip pick up time");
            Log.d(TAG, "updateTripDetail: trip pick up time empty");

            return;
        }

        Log.d(TAG, "updateTripDetail: 283");

        if (TextUtils.isEmpty(autoCompleteTextView.getText().toString())) {
            autoCompleteTextView.setError("Please assign a driver");

            return;
        }

        Log.d(TAG, "updateTripDetail: 291");

//        if (source == null) {
//            dismiss();
//            return;
//        }
//
//        if (destination == null) {
//            dismiss();
//            return;
//        }
        Log.d(TAG, "updateTripDetail: 302");


//        Driver driver = new Driver(autoCompleteTextView.getText().toString(),autoCompleteTextView.getText().toString()+"3232@gmail.com","9834598348",R.drawable.img6,null,"Incomplete");


         //everything is fine, update the trip now

        Log.d(TAG, "updateTripDetail: 299");
         dismiss();


    }

    public void resetFields(){

        trip_name.getText().clear();
        tripSrcAdrs.getText().clear();
        tripDestAdrs.getText().clear();
        tripPickupTime.getText().clear();
    }
}
