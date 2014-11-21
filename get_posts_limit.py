#!/usr/bin/env python
"""
A simple example script to get all posts on the user's timeline.
Using the client library Facebook Python SDL
https://github.com/pythonforfacebook/facebook-sdk
"""
import facebook
import requests
import time
from calendar import timegm
from datetime import datetime
import codecs

##
## Gets a bigger image for the post
##
def get_photo(objectID):
    try:
        pictures = graph.request(objectID)
        ##pictures = graph.api_request()
        numImages = pictures.get('images')
        for img in numImages:
            if img.get('width') > 130 and img.get('width') < 200:
                url = img.get('source')            
                return url
                break
    except facebook.GraphAPIError as e:
        #print "Error:", e
        return False


##
## Formats the text for ago with an s for plural or not.
##
def pluralize(count, text ):
    if count == 1:
        return "%s %s ago" %(count ,text)
    else:
        return "%s %ss ago" %(count, text)

##
## Formats the date and time as needed for the page
##
def timeAgo(utcDateTimeStr):
    ## Current time in UTC
    utcnowTimeStruct = time.gmtime();
    utcnowTimestamp = timegm(utcnowTimeStruct)
    
    then = utcDateTimeStr[:-5]
    thenTimestamp = timegm(time.strptime(then, "%Y-%m-%dT%H:%M:%S"))
    thenTimeStruct = time.gmtime(thenTimestamp)
    
    ## Calculate Difference
    diff = utcnowTimestamp - thenTimestamp
    days = int(diff) / 86400
    hours = int(diff) / 3600 % 24
    minutes = int(diff) / 60 % 60
    
    if days > 1 :
        return time.strftime("%B %d", thenTimeStruct)
    elif days == 1:
        return pluralize(days, "day")
    elif hours > 0:
        return pluralize(hours, "hour")
    else:
        return pluralize(minutes, "minute")
    
##
## Format markup for Slick slider
##
def format_post_for_slick_slider(post):
    markup = ''
    ago = timeAgo(post['created_time'])
    if post.get('name') is not None:
        altText = post.get('name')
        if post.get('message') is None:
            message = altText
        else:
            ## Print only 200 characters.
            if len(post.get('message')) > 200:
                message = post.get('message')[0:200] + '...'
            else:
                message = post.get('message')           
    else:
        altText = "Fb image post"
        if post.get('message') is None:
            message = ''
        else:
            ## Print only 200 characters.
            if len(post.get('message')) > 200:
                message = post.get('message')[0:200] + '...'
            else:
                message = post.get('message')
    
    if message == '' and post.get('picture') is None:
        return False
    else:
        divTitle = "<div class=\"title\"><h4><img src=\"http://www.usna.edu/CMS/_standard3.0/_files/img/facebook-color.png\" alt=\"FB logo\" />qUSNavalAcademy</h4><span class=\"timestamp\">%s</span></div>\n" %ago
        if post.get('picture') is not None:
            ## FB picture if type is photo. Get a larger image other than the thumbnail.
            if post.get('type') == "photo":
                smallImgLink = get_photo(post.get('object_id'))
            else:
                smallImgLink = False
                
            if smallImgLink is not False:         
                text = "<p> %s <a href=\"%s\"><img class=\"FBphoto\" src=\"%s\" alt=\"%s\" /></a></p>\n" %(message, post.get('link'), smallImgLink, altText)
            else:
                text = "<p> %s <a href=\"%s\"><img class=\"FBphoto\" src=\"%s\" alt=\"%s\" /></a></p>\n" %(message, post.get('link'), post.get('picture'), altText)
        else:
            text = "<p> %s </p>\n" %(message)
    
        markup = "<div class=\"feed-container\">\n%s %s</div>\n"%(divTitle, text)
        return markup
    
# You'll need an access token here to do anything. You can get a temporary one
# here: https://developers.facebook.com/tools/explorer/
appId = '...'
appSecret = '...'
access_token = facebook.get_app_access_token(appId, appSecret)
user = 'USNavalAcademy'
try:
    graph = facebook.GraphAPI(access_token)
    profile = graph.get_object(user)
    posts = graph.get_connections(profile['id'], 'posts')
    # Print requests.
    # Format each post in the collection we receive from
    # Facebook. I just need the latest 5 posts.
    # Print to a file   
    writePosts = ''
    for index, post in enumerate(posts['data']):
        if index < 6:
            try:
                if format_post_for_slick_slider(post=post) is not False:
                    writePosts += format_post_for_slick_slider(post=post)                
            except KeyError:
                print "Key error encountered",KeyError
        else:
            break
    f = codecs.open("FBPosts.txt", encoding="utf-8", mode="w")
    f.write(writePosts)
    f.close()
except facebook.GraphAPIError as e:
    print "Error:", e