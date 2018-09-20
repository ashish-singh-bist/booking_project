class Config:
    def __init__(self):
      ''' Constructor for this class. '''
      # Create some member
      self.host="localhost"
      self.database="ean_scraping"
      self.user="root"
      self.password="tick98"
      self.port=""
      self.charset="utf8"
      self.mongo_host="192.168.1.117"
      self.mongo_port="27017"
      self.mongo_database="booking_project"
      self.html_dir_path="/var/www/html/master/booking_project/parsing_scripts/html_dir/"
      self.arr_length_stay=[1,2,3,5,7]
