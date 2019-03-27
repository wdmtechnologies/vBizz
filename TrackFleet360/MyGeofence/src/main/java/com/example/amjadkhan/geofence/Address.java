package com.example.amjadkhan.geofence;

public class Address {

    private String source;
    private String destination;

    public Address(String source, String destination) {
        this.source = source;
        this.destination = destination;
    }

    public String getDestination() {
        return destination;
    }

    public String getSource() {
        return source;
    }


}
