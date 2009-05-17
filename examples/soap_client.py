# -*- coding: utf-8 -*-
from SOAPpy import WSDL

WSDLFile = 'http://localhost/sit/soap.php?wsdl';

user = 'admin';
password = 'novell';

_server = WSDL.Proxy(WSDLFile);
print _server.add(1, 5);
result = _server.sit_login(user, password);
print result;
print result['status']['value'];

if result['status']['value'] == 0:
    print 'Logged in';
    sessionid = result['sessionid'];
    print "Session id "+sessionid;
    _server.show_methods();
    incidentsResult = _server.list_incidents(sessionid, 0, 1);
    incidents = incidentsResult['incidents'];
    for i in incidents:
        print "Incident: ",i['incidentid']," ",i['title'];
        #print i['incidentid'],"test";
        #print i['title'];
        #print i;
        print '';
    result = _server.logout(sessionid);
    if result['status']['value'] == 0:
        print "Logout Sucessful";
    else:
        print "Logout failed";
else:
    print 'Login failed';
