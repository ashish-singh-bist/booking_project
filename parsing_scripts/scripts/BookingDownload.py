from Master import Master
import re
import sys, getopt
import os
import json
import random
import time

class BookingDownload(Master):
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
    if self.obj_helper.isFileExists( "./html_dir/" + file_name ):
      html = self.obj_helper.readFile( "./html_dir/" + file_name ) 
      print( "File Already exists" )
      return {}
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
      print( "Saved successfully................." )
      self.obj_helper.writeFileNewUTF( "./html_dir/"+file_name , html )   #save log in log file  
      return {}