package com.example.amjadkhan.geofence.account;

public class Alert {

    String type;
    String location;
    String time;

    public Alert(String type, String location, String time) {
        this.type = type;
        this.location = location;
        this.time = time;
    }

    public String getType() {
        return type;
    }

    public String getLocation() {
        return location;
    }

    public String getTime() {
        return time;
    }
}
