from Master import Master
import re
import sys, getopt
import os
import json
import random
import time

class Booking(Master):
  def __init__(self):
    Master.__init__(self)
    #self.proxy = {}
    self.params = {}
    #self.params['proxy_ip'] = { 'http': 'socks5://127.0.0.1:9050',}
    self.params['return_error_page'] = 1
  def parseProductDetails(self,url,file_name,checkin_date,checkout_date):
    data_dic = {}
    ###################
    # if self.proxy_list:      
    #   proxy_hash = self.proxy_list[ random.randint(0,len(self.proxy_list)-1) ]
    #   proxy_ip = proxy_hash['proxy_ip']
    #   self.params['proxy_ip'] = { 'http': proxy_ip,}
    ###################
    CHROME_UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
    self.params['headers'] = { 'user-agent':CHROME_UA , 'host' : 'www.booking.com' , 'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8','Accept-Language':'en-US,en;q=0.9','Accept-Encoding':'gzip, deflate, br','Upgrade-Insecure-Requests':1 }
    
    html = ""
    #html = self.obj_helper.readFile( "booking.html" ) 
    if self.obj_helper.isFileExists( "./html_dir/" + file_name ):
      html = self.obj_helper.readFile( "./html_dir/" + file_name ) 
      print( "File Already exists" )      
    else:      
      html = self.obj_req.getPage( "GET", url , self.params )
    if not html:
      print("\nCould not get html for url:"+url)
      self.obj_helper.writeFile( "Log.txt" , "\nCould not get html for:"+url )   #save log in log file
      return {}   
    
    result_dict = {}
    if html:
      html = re.sub(r'&amp;', '&', html,flags=re.S|re.M)
      html = re.sub(r'&nbsp;', ' ', html,flags=re.S|re.M)
      if not self.obj_helper.isFileExists( "./html_dir/"+file_name ):
        print( "Saved successfully................." )
        self.obj_helper.writeFileNewUTF( "./html_dir/"+file_name , html )   #save log in log file  
      
      dict_hotel_info = {}

      #html = re.sub(r'\n+', '', html,flags=re.S|re.M)
      #self.obj_helper.writeFileNewUTF( "booking_new.html" , html )   #save log in log file
      #exit()
      #<h2 class="hp__hotel-name" id="hp_hotel_name">Le Méridien Goa, Calangute</h2>
      title = self.obj_helper.getContainerText(html,'h2','id','hp_hotel_name')
      if title:        
        dict_hotel_info['hotel_name'] = self.obj_helper.removeHtml(title)

      #<span class="hp_address_subtitle js-hp_address_subtitle jq_tooltip " data-source="top_link" data-coords="," data-node_tt_id="location_score_tooltip" title="">
      location = self.obj_helper.getContainerText(html,'span','class','hp_address_subtitle')
      if location:
        dict_hotel_info['location'] = self.obj_helper.removeHtml(location)
      #<span class="hp__hotel_ratings">
      #star_ratings = self.obj_helper.getContainerText(html,'span','class','hp__hotel_ratings')
      star_ratings = self.obj_helper.getContainerHtml(html,'span','class','hp__hotel_ratings')      
      if star_ratings:
        #<svg class="bk-icon -sprite-ratings_stars_4"
        arr_temp_class = self.obj_helper.getAttributeValue( star_ratings , 'svg' , 'class' )
        for temp_class in arr_temp_class:
          m = re.search(r'ratings_stars_(\d+)', temp_class,re.S)
          if m:          
            dict_hotel_info['hotel_stars'] = m.group(1)     


      #booking.env.b_map_center_latitude = 15.55752173;      
      m = re.search(r'booking.env.b_map_center_latitude\s*=\s*(.+?)\;', html,re.S)
      if m:
        dict_hotel_info['latitude'] = m.group(1)

      #booking.env.b_map_center_longitude = 73.75393242; 
      m = re.search(r'booking.env.b_map_center_longitude\s*=\s*(.+?)\;', html,re.S)
      if m:
        dict_hotel_info['longitude'] = m.group(1)


      dict_hotel_info['hotel_id'] = self.getHotelId(html)      

      # #<input type="hidden" name="hotel_id" value="3433374" />      
      # input_tag = self.obj_helper.getContainerHtml(html,'input','name','hotel_id')
      # if input_tag:
      #   dict_hotel_info['hotel_id'] = self.obj_helper.getAttributeValue( input_tag , 'input' , 'value' )
      # else:
      #   m = re.search('window\.utag_data\s*=\s*(\{.+?\})', html,re.S)
      #   if m and m.group(1):          
      #     dict_hotel_info['hotel_id'] = m.group(1)      

      #<span  class=" review-score-widget   review-score-widget__20     hp_main_score_badge  "  >
      span_html = self.obj_helper.getContainerHtml(html,'span','class','hp_main_score_badge')
      if span_html:
        #<span aria-label="Scored 8.7 " class="review-score-badge" role="link">8.7</span>
        rating_html = self.obj_helper.getContainerText(span_html,'span','class','review-score-badge')
        if rating_html:
          dict_hotel_info['booking_rating'] = self.obj_helper.removeHtml(rating_html)
      
      ##################################################################
      result_dict['checkin_date'] = checkin_date
      result_dict['checkout_date'] = checkout_date
      #checkin and checkout date is input string so no need to parse it from html
      # #<a href="#" class="av-summary-value av-summary-checkin bui-date__title bui-link bui-link--primary hp-dates-summary__date">
      # check_in_date = self.obj_helper.getContainerText(html,'a','class','av-summary-checkin')
      # if check_in_date:        
      #   #result_dict['checkin_date'] = self.obj_helper.removeHtml(check_in_date)
      #   checkin_date = self.obj_helper.removeHtml(check_in_date)
      #   #Fr., 14. Sept. 2018
      #   result_dict['checkin_date'] = self.getCorrectDateFormat(checkin_date)
      
      # #<a href="#" class="av-summary-checkout bui-date__title bui-link bui-link--primary hp-dates-summary__date">
      # check_out_date = self.obj_helper.getContainerText(html,'a','class','av-summary-checkout')
      # if check_out_date:        
      #   check_out_date = self.obj_helper.removeHtml(check_out_date)
      #   result_dict['checkout_date'] = self.getCorrectDateFormat(check_out_date)
      ##################################################################



      #<a href="#" class="bui-date__title bui-link bui-link--primary hp-dates-summary__date av-summary-value av-summary-guests">2 adults</a>
      number_of_guests = self.obj_helper.getContainerText(html,'a','class','av-summary-guests')      
      if number_of_guests:        
        number_of_guests = self.obj_helper.removeHtml(number_of_guests)
        #Fr., 14. Sept. 2018
        result_dict['number_of_guests'] = number_of_guests
      
      # #<a href="#" class="av-summary-checkout bui-date__title bui-link bui-link--primary hp-dates-summary__date">
      # m = re.search('<a[^>]+class\W+[^\"\']*av-summary-checkout\W[^>]*>(.+?<\/span>)', html,re.S)
      # if m:
      #   print
      #   temp_checkout = m.group(1)        
      #   #<span class="bui-date__subtitle">
      #   length_stay = self.obj_helper.getContainerText(temp_checkout,'span','class','bui-date__subtitle')        
      #   if length_stay:
      #     result_dict['length_stay'] = self.obj_helper.removeHtml(length_stay)


      #b_rooms_available_and_soldout: [{....],
      m = re.search('b_rooms_available_and_soldout\s*:\s*(\[.*?\])\s*,\n', html,re.S)
      if m and m.group(1):
        room_details_json = m.group(1)        
        #print( "Room Details Json"+room_details_json )
        arr_room_details_dict = json.loads(room_details_json)
        arr_room_data_final = []

        for dict_room_details in arr_room_details_dict:
          dict_room_info = {}
          #dict_room_info['room_type'] = dict_room_details['b_name']
          room_type = dict_room_details['b_name']
          dict_room_info[room_type] = {}         
          #print(room_type)
          arr_price_info = []
          arr_b_blocks = dict_room_details['b_blocks']
          for b_block in arr_b_blocks:
            dict_price_info = {}            
            if 'b_raw_price' in b_block and b_block['b_raw_price']:
              dict_price_info['raw_price'] = b_block['b_raw_price']
              #print(dict_price_info['raw_price'])
            if 'b_mealplan_included_name' in b_block and b_block['b_mealplan_included_name']:
              dict_price_info['mealplan_included_name'] = b_block['b_mealplan_included_name']
            if 'b_cancellation_type' in b_block and b_block['b_cancellation_type']:
              dict_price_info['cancellation_type'] = b_block['b_cancellation_type']
            if 'b_nr_stays' in b_block and b_block['b_nr_stays']:
              dict_price_info['nr_stays'] = b_block['b_nr_stays']
            if 'b_max_persons' in b_block and b_block['b_max_persons']:
              dict_price_info['max_persons'] = b_block['b_max_persons']

            if 'b_block_id' in b_block and b_block['b_block_id']:
              b_block_id = b_block['b_block_id']              
              room_equp_desc_price_dict = self.parseRoomEqupDetails(html,b_block_id)
              #print(room_equp_desc_price_dict)
              #'dict_room_equipment':dict_room_equipment , 'dict_price_desc':dict_price_desc
              if 'dict_room_equipment' in room_equp_desc_price_dict and room_equp_desc_price_dict['dict_room_equipment']:
                dict_room_info[room_type]['room_equipment'] = room_equp_desc_price_dict['dict_room_equipment']
              if 'dict_price_desc' in room_equp_desc_price_dict and room_equp_desc_price_dict['dict_price_desc']:                
                for temp_key in room_equp_desc_price_dict['dict_price_desc']:
                  dict_price_info[temp_key] = room_equp_desc_price_dict['dict_price_desc'][temp_key]                
            arr_price_info.append(dict_price_info)
          dict_room_info[room_type]['price_info'] = arr_price_info          
          arr_room_data_final.append(dict_room_info)
          result_dict['price_details'] = arr_room_data_final
    ##################
    hotel_equipments = self.parseHotelEqupDetails(html)    
    ##################        
    final_result = {}
    final_result['hotel_info'] = dict_hotel_info    
    final_result['hotel_info']['hotel_equipments'] = hotel_equipments
    final_result['room_price_details'] = {}
    if 'price_details' in result_dict and result_dict['price_details']:
      final_result['room_price_details']['price_details'] = result_dict['price_details']
    else:
      final_result['room_price_details']['price_details'] = []
    if 'number_of_guests' in result_dict:
      final_result['room_price_details']['number_of_guests'] = result_dict['number_of_guests']
    final_result['room_price_details']['checkin_date'] = result_dict['checkin_date']
    final_result['room_price_details']['checkout_date'] = result_dict['checkout_date']
    
    #if 'checkin_date' in result_dict and 'checkout_date' in result_dict:
    #  key = result_dict['checkin_date']+"To"+result_dict['checkout_date']      
    #  final_result[key] = result_dict
    return final_result

  def parseHotelEqupDetails(self,html):    
    dict_hotel_equipments = {}

    #<div class="facilitiesChecklist">
    temp_html = self.obj_helper.getContainerHtml(html,'div','class','facilitiesChecklist')
    if temp_html:
      #<div class="facilitiesChecklistSection " data-section-id="5" data-et-view="">      
      arr_div_html = self.obj_helper.getContainerData(temp_html,'div','class','facilitiesChecklistSection')
      for div_html in arr_div_html:
        facility_key = None
        #<h5 data-et-view="">
        m = re.search('<h5[^>]*>(.+?)<\/h5>',str(div_html),re.S)
        if m:
          facility_key = self.obj_helper.removeHtml(m.group(1))
        if facility_key:
          dict_hotel_equipments[facility_key] = {}          
          #<li  data-photo-preview-facility-141-room="141">
          arr_li_html = self.obj_helper.getHtmlByTag(str(div_html),'li')
          for li_html in arr_li_html:
            key = self.obj_helper.removeHtml(str(li_html))            
            dict_hotel_equipments[facility_key][key] = 1    
    return dict_hotel_equipments

  def getHotelId(self,html):    
    #<input type="hidden" name="hotel_id" value="3433374" />    
    input_tag = self.obj_helper.getContainerHtml(html,'input','name','hotel_id')    
    if input_tag:
      return self.obj_helper.getAttributeValue( input_tag , 'input' , 'value' )
    else:
      m = re.search('window\.utag_data\s*=\s*(\{.+?\})', html,re.S)
      if m and m.group(1):          
        return m.group(1)
    return ""

  def getCorrectDateFormat(self,date_str):
    from dateutil.parser import parse
    m = re.search('\s+(.+)',date_str,re.S)
    if m:
      date_str = m.group(1)
      date_str = re.sub(r'\.', '', date_str,flags=re.S|re.M)           
      #Mon Feb 15 2010
      temp_dt = parse(date_str)      
      temp_dt = temp_dt.strftime('%Y-%m-%d')
      return temp_dt


  def parseRoomEqupDetails(self,html,b_block_id):
    #<tr data-block-id="48185003_124142933_2_33_0"
    # tr_html = self.obj_helper.getContainerHtml(html,'tr','data-block-id',b_block_id)
    # if tr_html:
    #   print(tr_html)
    #   exit()    

    dict_room_equipment = {}
    #m = re.search('<tr[^>]+data-block-id\W+'+b_block_id+'\W[^>]*>(.+?)<\/tr>', html,re.S)
    #<tr data-block-id="48185003_124142933_2_33_0"
    tr_html = self.obj_helper.getContainerHtml(html,'tr','data-block-id',b_block_id)
    if tr_html:      
      temp_html = tr_html
      #• Zimmer mit Verbindungstür verfügbar
      temp_html = re.sub(r'•', '', temp_html)      
      #<div class="hprt-facilities-block" data-component="hotel/new-rooms-table/highlighted-facilities">
      div_html = self.obj_helper.getContainerHtml(temp_html,'div','class','hprt-facilities-block')
      if div_html:
        #<span class="hprt-facilities-facility" data-name-en="">
        arr_span_tag = self.obj_helper.getContainerData(div_html,'span','class','hprt-facilities-facility')
        for span_tag in arr_span_tag:                      
          temp_val = self.obj_helper.removeHtml(str(span_tag))
          #<i class="hprt-facilities-icon bicon-roomsize"></i>
          if 'bicon-roomsize' in str(span_tag):
            dict_room_equipment['room_size'] = temp_val
          else:
            dict_room_equipment[temp_val] = 1
      
      dict_price_desc = {}
      #<ul class="hprt-conditions">
      ul_html = self.obj_helper.getContainerHtml(temp_html,'ul','class','hprt-conditions')
      if ul_html:
        arr_li_html = self.obj_helper.getHtmlByTag(ul_html,'li')
        for li_html in arr_li_html:
          if 'goal:hp_rt_hovering_mealplan' in str(li_html):
            dict_price_desc['mealplan_desc'] = self.obj_helper.removeHtml(str(li_html))
          if 'goal:hp_rt_hovering_free_cancellation' in str(li_html):
            dict_price_desc['cancellation_desc'] = self.obj_helper.removeHtml(str(li_html))
    return { 'dict_room_equipment':dict_room_equipment , 'dict_price_desc':dict_price_desc }


