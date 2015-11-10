# Target Audience

This documentation contains overview information on the solution level and very detailed technical information. The target audience consists of the following persons:

- Technology consultants
- System administrators

-----
    1.0 Introduction    
    2.0 Procedure   
    2.1 Pre-Installation Steps  
    2.2 Installation Steps  
    2.3 Configuration Steps 
    2.4 Push Order  

# 1.0 INTRODUCTION
Fetchr API extension in Opencart will improve your customer shopping experience with extended feature It create connectivity between your Opencart website and Fetchr to share the order information.

# 2.0 PROCEDURE
# 2.1 Pre-Installation Steps

Extension Download

Download opencart extension file from here http://support.fetchr.us/hc/en-us/articles/205732391-Opencart-Extension-V1-0

# 2.2 Installation Steps

1. Extract the file to your local machine.
2. Login as Admin.
3. Go to Extensions > Extensions Installer > Upload file (fetchr.ocmod.xml).
4. Go Extensions > Modifications > Click Refresh button.
5. Extensions > Extensions Installer > Upload file (fetchr_api_1.0.0.ocmod.zip).
6. Go Extensions > Modules, find Fetchr Api module and Click install button.
7. After installing Fetchr OpenCart extension, Click edit button to configure.

# 2.3 Configuration Steps

    Caution:
    ALWAYS remember to make a full database/files backup before installing any new modules!
    Note:
    In this interface you find 2 type of services.
    
- Fulfillment + delivery. 
- Select Fulfillment + Delivery for warehouse and delivery service.
- Delivery only.
- Select Delivery Only for delivery services.

1. Select Account Type, Service Type, Username and Password then Click Save button, as shown in the following figure.

![Image of Api](http://support.fetchr.us/hc/en-us/article_attachments/202249402/image00.png)

## 2.4 Push Order

    Caution:
    Test Fetchr in staging mode before go to live.
1. Go Sales > Orders, Click view order.

        Note:
        In order history section, change status from pending to Ready for Pick up.

2. Go to Extensions > Modules > Fetchr Api > Click Edit button.
3. Click push order.
4. Go to Order view page > History section >Click Tracking URL to continue the order.

    ##### [Click here](http://support.fetchr.us/hc/en-us/articles/205732391-Opencart-Extension-V1-0) to Download the Extension.

    ##### Click below link to Download the Document:

    [OpenCart-API-Extension_InstallationGuide_V1.0.pdf (200 KB)](http://support.fetchr.us/hc/en-us/article_attachments/202237701/OpenCart-API-Extension_InstallationGuide_V1.0.pdf)
    
