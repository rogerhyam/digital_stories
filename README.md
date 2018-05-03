# Digital Stories

These are notes to myself as much as anything and so I can't guarantee that if you follow all the steps it will work. You may have to problem solve some permissions and stuff.

_The problem:_ We want to run workshops where we have around 10 iPads and a bunch of videos. Participants can use an iPad to browse a list of videos and play the ones they are interested in. 

- We don't want them going off and browsing the internet or messing up other applications on the iPads.
- We don't want to spend ages loading the ever changing list of videos onto each iPad.
- We don't want to rely on having a good internet connection at the venue.
- We don't want to spend a fortune.

_The Solution:_ 

- Set up a Raspberry Pi as a WiFi wireless access point.
- Put a web server on the Pi that serves videos off a USB memory stick.
- Join the iPads to the Pi network by default.
- Lock the iPads down to just run Safari.
- Only permit viewing a single web site.
- Launch a webpage fullscreen from the home screen.

Using this approach all the iPads are set up the same and relatively easily managed. Videos can be easily updated on a memory stick. The Pi and memory stick are so cheap we can have a complete backup system for when things go wrong.

## Making Raspberry Pi 3 its own WiFi access point. 

Note this makes the Pi an access point but we don't take it as far as being a bridge to the internet. There is no uplink it is a stand alone appliance.

This is a good description of the process.

https://www.raspberrypi.org/documentation/configuration/wireless/access-point.md

Take a clean copy of Raspbian Stretch Lite.

sudo apt-get update
sudo apt-get upgrade
sudo apt-get install dnsmasq hostapd
sudo systemctl stop dnsmasq
sudo systemctl stop hostapd

sudo nano /etc/dhcpcd.conf

And add 

 interface wlan0
    static ip_address=192.168.4.1/24

sudo service dhcpcd restart

sudo mv /etc/dnsmasq.conf /etc/dnsmasq.conf.orig  
sudo nano /etc/dnsmasq.conf

interface=wlan0      # Use the require wireless interface - usually wlan0
dhcp-range=192.168.4.2,192.168.4.20,255.255.255.0,24h

sudo nano /etc/hostapd/hostapd.conf

interface=wlan0
driver=nl80211
ssid=DigitalStories
hw_mode=g
channel=7
wmm_enabled=0
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
wpa=2
wpa_passphrase=andymax1
wpa_key_mgmt=WPA-PSK
wpa_pairwise=TKIP
rsn_pairwise=CCMP

[Consider changing the SSID and passphrase!]

sudo nano /etc/sysctl.conf

And uncomment 
net.ipv4.ip_forward=1

sudo iptables -t nat -A  POSTROUTING -o eth0 -j MASQUERADE

sudo sh -c "iptables-save > /etc/iptables.ipv4.nat"

USB Stick should be FAT format only and will be automounted by pmount

## Adding  Apache web server and PHP to the Pi

sudo apt-get install apache2 -y
sudo apt-get install php libapache2-mod-php -y

## Creating the website

Copy the code from the repository to the /var/www/html You could just clone it if you have installed git.

cd /var/www/html
git clone https://github.com/rogerhyam/digital_stories.git .

[Consider changing the name of the shutdown php file so workshop participants can't find it!]

Create a symbolic link called videos to where the USB stick will be auto mounted.

ln -s /media/usb/videos videos

## Preparing the USB memory stick

The memory stick should be FAT formatted and named 'DIGITALSTORIES'.

There should be a directory in the root called 'videos' which contains the mp4 format videos.

e.g. 

001_myvideo.mp4

For each video include two other files with the same name but extra endings.

001_myvideo.mp4.jpg should be a square image to be used as a thumbnail. 300px by 300px is a good size.

001_myvideo.mp4.txt should be a plain text file (UTF-8) with two lines in it. The first line is the title of the video. The second line is the description.

Note that the videos are listed in alphabetical order of file names so it is good to start them with zero spaced numbers. If you think you are going to move them around then you could start by naming them 010_xyz.mp4 020_abc.mp4 etc then it is easy to insert new ones.

## Enabling shutdown of the Pi over the WiFi

We don't want to repeatedly pull the power cable as it will eventually corrupt the SD card and we don't want to plug in a monitor & keyboard so we have a php script that will kill the server and have it on hidden URL. This is something we would never do on a "real" server.

sudo visudo

Add the following line below "pi ALL etc." and exit the visudo editor:

www-data ALL = NOPASSWD: /sbin/shutdown

sudo nano /var/www/shutdown.php

Absolute minimum contents of the shutdown.php file:
   
system('sudo /sbin/shutdown -h now');   

## Preparing the iPads (one off procedure)

On the iPad go into settings and join the DigitalStories WiFi network (assuming the Pi is running).

Open Safari and go to http://192.168.4.1 - you should see the videos and it should work but not be full screen.

Click on the sharing link next to the address bar and choose "Add to Home Screen".

In the Settings app go General > Accessibility > Guided Access and turn it on. (Remember the passcode)

Go to the Home Screen and tap the Digital Stories icon.

Triple tap the home button.

You should now be in the video player, full screen with no way to leave it except triple tapping the home button and entering the passcode. 

## On the day setup

Plug the Raspberry Pi into the power (without the USB stick in it) and wait a couple of minutes for it to boot. Maybe start getting the iPads out.

Plug the USB stick into the Raspberry Pi.

On each iPad:

- Settings > Wifi > DigitalStories
- Home Screen > DigitalStories 
- Triple tap home button.

## On the day teardown

On one of the iPads (or your phone) open Safari and go to http://192.168.4.1/maxandy1.php then wait 2 minutes.

Unplug and pack away.






