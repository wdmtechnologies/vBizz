package com.example.amjadkhan.geofence;

import com.google.android.gms.maps.model.LatLng;
import com.google.gson.annotations.SerializedName;

public class Employee {


    @SerializedName("emp_id")
    private String eId;

    @SerializedName("e_name")
    private String eName;

    @SerializedName("e_address")
    private String eAddress;

    @SerializedName("e_city")
    private String eCity;

    @SerializedName("e_country")
    private String eCountry;

    @SerializedName("e_email")
    private String eEmail;

    @SerializedName("e_contact")
    private String eContact;

    @SerializedName("e_rating")
    private String eRating;

    @SerializedName("lat")
    private String lat;

    @SerializedName("lng")
    private String lng;


    @SerializedName("profile_img")
    private String eImg;

    @SerializedName("trip_alloted")
    private String tripAlloted;

    @SerializedName("status")
    private String status;


    public String geteId() {
        return eId;
    }

    public String geteName() {
        return eName;
    }

    public String geteAddress() {
        return eAddress;
    }

    public String geteCity() {
        return eCity;
    }

    public String geteEmail() {
        return eEmail;
    }

    public String geteImg() {
        return eImg;
    }

    public String getTripAlloted() {
        return tripAlloted;
    }

    public boolean isOnline() {
        if (status.equals("1")){
            return true;
        }
        else{
            return false;
        }
     }

    private double getLat() {
        return Double.valueOf(lat);
    }

    private double getLng() {
        return Double.valueOf(lng);
    }



    public String geteCountry() {
        return eCountry;
    }

    public String geteContact() {
        return eContact;
    }

    public String geteRating() {
        return eRating;
    }

    public int getOnlineStatus() {
        if (status.equals("1")){
            return 1;
        }
        else if (status.equals("2")){
            return 2;
        }
        else{
            return 0;
        }
     }

     public LatLng getCurrentLatLng() {
           return new LatLng(getLat(),getLng());
     }

    @Override
    public String toString() {
        return "Employee{" +
                "eId='" + eId + '\'' +
                ", eName='" + eName + '\'' +
                ", eAddress='" + eAddress + '\'' +
                ", eCity='" + eCity + '\'' +
                ", eEmail='" + eEmail + '\'' +
                ", eImg='" + eImg + '\'' +
                ", tripAlloted='" + tripAlloted + '\'' +
                ", status='" + status + '\'' +
                '}';
    }

}
