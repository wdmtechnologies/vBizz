package com.example.amjadkhan.geofence.account;

import com.google.gson.annotations.SerializedName;

public class Driver {

    @SerializedName("emp_id")
    private String empId;

    @SerializedName("e_name")
    private String name;

    @SerializedName("e_address")
    private String address;

    @SerializedName("e_city")
    private String city;

    @SerializedName("e_country")
    private String country;

    @SerializedName("e_contact")
    private String contact;

    @SerializedName("e_email")
    private String email;

    @SerializedName("e_password")
    private String password;

    @SerializedName("trip_alloted")
    private int tripAlloted;

    @SerializedName("status")
    private String status;

    @SerializedName("created_by")
    private String createBy;



    public String getName() {
        return name;
    }


    public String getPhone() {
        return contact;
    }



    public String getEmail() {
        return email;
    }



    public String getAddress() {
        return address;
    }

    public String getCity() {
        return city;
    }

    public String getCountry() {
        return country;
    }

    public String getEmpId() {
        return empId;
    }

    public String getPassword() {
        return password;
    }

    public void setTripAlloted(int tripAlloted) {
        this.tripAlloted = tripAlloted;
    }

    public int isTripAlloted() {
        return tripAlloted;
    }
}
