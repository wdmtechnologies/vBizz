package com.example.amjadkhan.geofence.account;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.app.AlertDialog;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;

import com.example.amjadkhan.geofence.home.HomeActivity;
import com.example.amjadkhan.geofence.login.LoginActivity;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.utils.Session;
import com.google.gson.annotations.SerializedName;

import butterknife.BindView;
import butterknife.ButterKnife;


/**
 * A simple {@link Fragment} subclass.
 */
public class ProfileFragment extends Fragment implements ProfileFragmentRecyclerAdapter.ItemClickListener{
    private static final String TAG = "ProfileFragment";

    Activity activity;
    @BindView(R.id.rv_profile_fragment)
    RecyclerView recyclerView;
    Context appContext;
    @BindView(R.id.shadow_view)
    View shadowView;
    Session session;



    public ProfileFragment() {
        // Required empty public constructor
    }

    public static ProfileFragment newInstance() {

        Bundle args = new Bundle();
        args.putString("name","ProfileFragment");

        ProfileFragment fragment = new ProfileFragment();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        session = new Session(getActivity());
        Admin admin = new Admin("Alex","alex123@gmail.com","C 221/3, Wisconsin","USA","9833225566");

    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof HomeActivity) {
            activity = (HomeActivity)context;
            appContext = context.getApplicationContext();
            Log.d(TAG, "onAttach: "+appContext);
            Log.d(TAG, "onAttach: "+activity);
        }
    }

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View rootView = inflater.inflate(R.layout.fragment_profile, container, false);
        ButterKnife.bind(this ,rootView);
        return rootView;
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP ) {
            shadowView.setVisibility(View.GONE);
        }

         Log.d(TAG, "onViewCreated: "+this);
        recyclerView.setAdapter(new ProfileFragmentRecyclerAdapter(this));
        recyclerView.addItemDecoration(new DividerItemDecoraton(getActivity().getApplicationContext()));
        recyclerView.setHasFixedSize(true);


    }

    @Override
    public void onItemClick(int position) {

        switch (position) {

            case 0:
                 showProfileView();
                 break;

            case 1:
                Intent intent = new Intent(getActivity(), DriverListActivity.class);
                startActivity(intent);
                break;

            case 3:
                Intent helpIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://trackfleet360.com/index.php/contact"));
                startActivity(helpIntent);
                break;

            case 4:
                if (session != null) {
                    session.destroySession();
                }
                startActivity(new Intent(getActivity(),LoginActivity.class));
                getActivity().finish();
                break;



        }


    }


    private void showProfileView() {

        final AlertDialog alertDialog = new AlertDialog.Builder(getActivity()).setView(R.layout.dialog_admin_profile).create();
        alertDialog.show();

        int width = getResources().getDimensionPixelSize(R.dimen.width_profile_dialog);
        Log.d(TAG, "showProfileView: "+width);
        int height = getResources().getDimensionPixelSize(R.dimen.height_profile_dialog);
        Log.d(TAG, "showProfileView: "+height);

        alertDialog.getWindow().setLayout(width,height);


       EditText etName = alertDialog.findViewById(R.id.et_name_profile);
       EditText etEmail = alertDialog.findViewById(R.id.et_email_profile);
       EditText etAddress = alertDialog.findViewById(R.id.et_address_profile);
       EditText etContact = alertDialog.findViewById(R.id.et_contact_profile);
       EditText etCountry = alertDialog.findViewById(R.id.et_country_profile);


       etName.setText("Vivian Hales");
       etEmail.setText("halesvivian@gmail.com");
       etAddress.setText("Wisconsin, USA");
       etContact.setText("42323434");
       etCountry.setText("USA");



       Button btnUpdate = alertDialog.findViewById(R.id.btn_update_detail_profile);
       ImageButton btnCancel = alertDialog.findViewById(R.id.ib_cancel_dialog_profile);

       btnCancel.setOnClickListener(new View.OnClickListener() {
           @Override
           public void onClick(View v) {
               alertDialog.dismiss();
           }
       });



    }


     static class Admin {

        @SerializedName("name")
        String name;
        @SerializedName("email")
        String email;
        @SerializedName("address")
        String address;
        @SerializedName("country")
        String country;
        @SerializedName("contact")
        String contact;

         public Admin(String name, String email, String address, String country, String contact) {
             this.name = name;
             this.email = email;
             this.address = address;
             this.country = country;
             this.contact = contact;
         }

         public String getName() {
             return name;
         }

         public void setName(String name) {
             this.name = name;
         }

         public String getEmail() {
             return email;
         }

         public void setEmail(String email) {
             this.email = email;
         }

         public String getAddress() {
             return address;
         }

         public void setAddress(String address) {
             this.address = address;
         }

         public String getCountry() {
             return country;
         }

         public void setCountry(String country) {
             this.country = country;
         }

         public String getContact() {
             return contact;
         }

         public void setContact(String contact) {
             this.contact = contact;
         }
     }
}

