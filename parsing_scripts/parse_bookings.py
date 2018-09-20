#!/usr/bin/python
# -*- coding: utf-8 -*- 

import os
import time
import sys, getopt
#import csv
#import requests
import re
#import hashlib
import json
import datetime
import hashlib
from datetime import timedelta, date
 #import threading

#####################
processoutput = os.popen("ps -A -L -F").read()
cur_script = os.path.basename(__file__)
res = re.findall(cur_script,processoutput)
if len(res)>2:
    print ("EXITING BECAUSE ALREADY RUNNING.\n\n")
    exit(0)
#####################

sys.path.append("modules")
sys.path.append("scripts")
sys.path.append("/usr/local/lib/python3.5/dist-packages")
from Master import Master
from Booking import Booking
obj_master = Master()  
obj_booking = Booking()
html_dir_path = obj_booking.obj_config.html_dir_path

# def getDateTimeObject(date_str):
#   datetime_object = datetime.strptime(date_str, '%Y-%m-%d')
#   return datetime_object
def getDateTimeObject(date_str):
  datetime_object = datetime.datetime.strptime(date_str, '%Y-%m-%d')
  return datetime_object


def parseAndSaveData(url,temp_file,checkin_date,checkout_date,temp_prop_id):  
  #temp_file = "5b9fb00152c92b16c314fb35-2018-09-21-1.html"
  print( "\nParsing start:"+str(datetime.datetime.now()) )
  print( "\nParsing file:"+temp_file )
  result = obj_booking.parseProductDetails(url,temp_file,checkin_date,checkout_date)
  print( "\nParsing End:"+str(datetime.datetime.now()) )  
  #print(str(result))
  #exit()
  #print(result)
  #return 1
  if 'hotel_info' in result:
    hotel_id = result['hotel_info']['hotel_id']
    result['hotel_info']['length_stay'] = length_stay
    #showing error while insert in hotel details
    del result['hotel_info']['hotel_equipments']
    record_count = obj_booking.obj_mongo_db.getCount( 'hotel_master' , { 'hotel_id':1 }, { 'hotel_id':hotel_id } )
    #record_count = obj_booking.obj_mongo_db.getCount( 'hotel_master' , { 'hotel_id':1 }, { 'hotel_id':hotel_id , 'checkin_date':checkin_date , 'length_stay':length_stay } )
    print("prop_id"+str(temp_prop_id))
    #################
    #https://www.booking.com/hotel/de/contel-koblenz.de.html?checkin=2018-09-18&checkout=2018-09-19&selected_currency=USD&group_adults=2
    m = re.search('(.+?)\?checkin',url,re.S)
    if m:
      result['hotel_info']['prop_url'] = m.group(1)
    #################
    if record_count:
      result['hotel_info']['prop_id'] = temp_prop_id
      #ret_id = obj_booking.obj_mongo_db.recUpdate( 'hotel_master' , result['hotel_info'] , { 'hotel_id':hotel_id } )
      #print( "\nUpdated in hotel_master The return id is"+str(ret_id) )
    else:
      result['hotel_info']['prop_id'] = temp_prop_id
      ret_id = obj_booking.obj_mongo_db.recInsert( 'hotel_master' , [ result['hotel_info'] ] )
      print( "\ninserted in hotel_master The return id is"+str(ret_id) )

    if 'room_price_details' in result:
      dict_room_price_details = result['room_price_details']
      for dict_price_details in dict_room_price_details['price_details']:        
        for key_room_type in dict_price_details:                  
          dict_room_info = dict_price_details[key_room_type]                  
          if 'price_info' in dict_room_info:
            arr_price_info = dict_room_info['price_info']
            if 'room_equipment' in dict_room_info:                      
              dict_room_info['hotel_id'] = hotel_id
              dict_room_info['room_type'] = key_room_type
              ###################
              dict_room_info['prop_id'] = temp_prop_id
              dict_room_info['checkin_date'] = getDateTimeObject(checkin_date)
              dict_room_info['number_of_days'] = length_stay
              ###################
              record_count = obj_booking.obj_mongo_db.getCount( 'room_details' , { 'hotel_id':1 }, { 'hotel_id':hotel_id,'room_type':key_room_type , 'checkin_date':checkin_date,'number_of_days':length_stay } )
              if not record_count:
                ret_id = obj_booking.obj_mongo_db.recInsert( 'room_details' , [ dict_room_info ] )
                print( "\ninserted in room_details The return id is"+str(ret_id) )                    
            available_only = ""            
                              
            for dict_price_info in arr_price_info:              
              #available_only = dict_price_info['max_persons']
              if 'nr_stays' in dict_price_info and dict_price_info['nr_stays']:
                available_only = dict_price_info['nr_stays']            
              ################
              #craeting these field manully
              dict_price_info['room_type'] = key_room_type
              dict_price_info['number_of_days'] = length_stay                    
              dict_price_info['number_of_guests'] = number_of_guests
              dict_price_info['hotel_id'] = hotel_id
              dict_price_info['checkin_date'] = getDateTimeObject(checkin_date)
              ################
              choices_str = "bp"
              if 'mealplan_included_name' in dict_price_info and dict_price_info['mealplan_included_name']:
                choices_str = dict_price_info['mealplan_included_name']
              if 'mealplan_desc' in dict_price_info and dict_price_info['mealplan_desc']:
                choices_str = choices_str + dict_price_info['mealplan_desc']
              if 'cancellation_type' in dict_price_info and dict_price_info['cancellation_type']:
                choices_str = choices_str + dict_price_info['cancellation_type']
              if 'cancellation_desc' in dict_price_info and dict_price_info['cancellation_desc']:
                choices_str = choices_str + dict_price_info['cancellation_desc']
              if 'other_desc' in dict_price_info and dict_price_info['other_desc']:
                for temp_other_desc in dict_price_info['other_desc']:
                  choices_str = choices_str + temp_other_desc
              #########################
              #set the key in redis cache
              str_to_md5 = str(hotel_id)+str(checkin_date)+str(key_room_type)+str(length_stay)+str(number_of_guests)+choices_str
              #for now not including price we will add it later...
              if 'raw_price' in dict_price_info and dict_price_info['raw_price']:
                str_to_md5 = str_to_md5+str(dict_price_info['raw_price'])
              temp_key_md5 = obj_booking.obj_helper.getMd5(str_to_md5)                      
              redis_value = obj_booking.obj_redis_cache.getKeyValue(temp_key_md5)
              if not redis_value:
                ###################
                dict_price_info['prop_id'] = temp_prop_id
                ###################                
                if 'raw_price' in dict_price_info:
                  ret_id = obj_booking.obj_mongo_db.recInsert( 'hotel_prices' , [ dict_price_info ] )
                  print( "\ninserted in hotel_prices The return id is"+str(ret_id) )
                  curr_date = datetime.datetime.now().date()
                  obj_booking.obj_redis_cache.setKeyValue(temp_key_md5,curr_date)
              #########################
            if available_only:
              dict_availability = {}
              dict_availability['hotel_id'] = hotel_id
              dict_availability['room_type'] = key_room_type
              dict_availability['checkin_date'] = getDateTimeObject(checkin_date)
              dict_availability['number_of_days'] = length_stay
              dict_availability['available_only'] = available_only
              #print(str(dict_availability))
              ###################
              dict_availability['prop_id'] = temp_prop_id
              ###################
              record_count = obj_booking.obj_mongo_db.getCount( 'rooms_availability' , { 'hotel_id':1 }, { 'hotel_id':hotel_id,'room_type':key_room_type,'checkin_date':checkin_date,'number_of_days':length_stay } )
              if not record_count:
                ret_id = obj_booking.obj_mongo_db.recInsert( 'rooms_availability' , [ dict_availability ] )
                print( "\ninserted in rooms_availability The return id is"+str(ret_id) )


if __name__ == '__main__':  
  ##########
  #temp_date_today = datetime.datetime(2018, 9, 17)
  # temp_date_today = datetime.datetime(2018, 9, 17)
  # property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1},{'updated_at':{ '$lt': temp_date_today }})
  # for property_url_row in property_url_rows:
  #     print(str(property_url_row['url']))
  # exit()
  ##########  
  temp_date_today = datetime.datetime(2018, 9, 18)#datetime.datetime.now()
  #property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1},{'updated_at':{ '$lt': temp_date_today }})
  property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1,'_id':1},{'updated_at':{ '$lt': temp_date_today }})  
  
  for property_url_row in property_url_rows:    
    temp_prop_id = property_url_row['_id']
    property_url = property_url_row['url']  
    is_parsed = obj_booking.obj_redis_cache.getKeyValue(temp_prop_id)
    if is_parsed:      
      print("\nThis url is parsed....skip and try for next")    
      #obj_booking.obj_redis_cache.deleteKeyValue(temp_prop_id)
      continue    
    #for today We are starting it this date. later we will set it as current date()
    start_date = datetime.datetime(2018, 10, 1).date()#datetime.datetime.now().date()
    end_date = datetime.datetime.now().date() + timedelta(days=365)
    while start_date < end_date:        
        checkin_date = str(start_date)        
        arr_length_stay = obj_booking.obj_config.arr_length_stay#[1,2,3,5,7]
        number_of_guests = 2
        for length_stay in arr_length_stay:
          checkout_date = str( start_date + timedelta(days=length_stay) )  # increase day one by one
          print( "checkin:"+checkin_date," length_stay:"+str(length_stay) )
          url = property_url+"?checkin="+str(checkin_date)+"&checkout="+str(checkout_date)+"&selected_currency=USD"+"&group_adults="+str(number_of_guests)
          #url = 'https://www.booking.com/hotel/de/contel-koblenz.de.html?checkin=2018-10-01&checkout=2018-10-03&selected_currency=USD&group_adults=2'  
          print(url)          
          ###############################################
          #old file format
          #temp_file = str(temp_prop_id)+"-"+str(checkin_date)+"-"+str(length_stay)+".html"
          #new file format
          url_md5 = obj_master.obj_helper.getMd5(url)
          temp_file = url_md5+".html"
          ##################
          #this is for local system
          temp_file = str(temp_prop_id)+"-"+str(checkin_date)+"-"+str(length_stay)+".html"
          ##################
          if not obj_master.obj_helper.isFileExists( html_dir_path + temp_file ):
            print("file not exists")
            obj_master.obj_helper.writeFile( "LogFileNotExists.txt" , "\nFile not exists for url"+url );
            continue
          ###############################################
          print(temp_file)          
          parseAndSaveData(url,temp_file,checkin_date,checkout_date,temp_prop_id)
          ###############
        start_date = start_date + timedelta(days=1)  # increase day one by one

    #obj_booking.obj_redis_cache.setKeyValue(temp_prop_id,1)
  exit()
