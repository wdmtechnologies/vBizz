package com.example.amjadkhan.geofence.account;

import android.content.Context;
import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.example.amjadkhan.geofence.R;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import de.hdodenhof.circleimageview.CircleImageView;

public class ProfileFragmentRecyclerAdapter extends RecyclerView.Adapter<ProfileFragmentRecyclerAdapter.CardViewHolder> {

    private Context context;
    private List<String> title ;
    private List<String> subtitle ;
    private List<Integer> imgRes ;

    private ItemClickListener listener;

    public interface ItemClickListener {
        void onItemClick(int position);

    }



    public ProfileFragmentRecyclerAdapter(ItemClickListener listener) {
        this.listener = listener ;
        title = new ArrayList<>();
        title.add("Profile");
        title.add("Drivers");
        title.add("Alerts");

        title.add("Help");
        title.add("Sign Out");

        subtitle = new ArrayList<>();
        subtitle.add("View and update your profile");
        subtitle.add("View add and remove drivers");
        subtitle.add("View trip and driver alerts");
        subtitle.add("Contact support");
        subtitle.add("Switch to other account");

        imgRes = new ArrayList<>();
        imgRes.add(R.drawable.admin);
        imgRes.add(R.drawable.ic_user_group);
        imgRes.add(R.drawable.ic_notifications_none);
        imgRes.add(R.drawable.ic_help);
        imgRes.add(R.drawable.ic_signout);






    }

    @NonNull
    @Override
    public CardViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
       return new CardViewHolder(LayoutInflater.from(parent.getContext()).inflate(R.layout.item_fragment_profile,null,false));

    }

    @Override
    public void onBindViewHolder(CardViewHolder holder, int position) {

            if (position == title.size() - 1) {
                holder.title_txt.setTextColor(Color.RED);
            }
           holder.title_txt.setText(title.get(position));
        holder.item_img.setImageResource(imgRes.get(position));
        holder.subtitle_txt.setText(subtitle.get(position));


    }

    @Override
    public int getItemCount() {
        return imgRes.size();
    }

     class CardViewHolder extends RecyclerView.ViewHolder {

        private static final String TAG = "CardViewHolder";
        @BindView(R.id.parent)
         RelativeLayout parentLayout;
        @BindView(R.id.civ_profile_frag_admin_pic)
        CircleImageView item_img;
        @BindView(R.id.tv_profile_frag_item_title)
        TextView title_txt;
        @BindView(R.id.tv_profile_frag_item_subtitle)
        TextView subtitle_txt;


        CardViewHolder(final View itemView) {
          super(itemView);
            Log.d(TAG, "CardViewHolder: "+this);
            ButterKnife.bind(this,itemView);


            parentLayout.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    listener.onItemClick(getAdapterPosition());

                }
            });





      }
  }
}
