VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
END
Attribute VB_Name = "STATION"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Attribute VB_Ext_KEY = "SavedWithClassBuilder" ,"Yes"
Attribute VB_Ext_KEY = "Top_Level" ,"Yes"
'local variable(s) to hold property value(s)
Private mvarkhz As String 'local copy
Private mvarusb As String 'local copy
Private mvarlsb As String 'local copy
Private mvardaid As String 'local copy
Private mvarcyc As String 'local copy
Private mvarcal As String 'local copy
Private mvarlat As String 'local copy
Private mvarlon As String 'local copy
Private mvarnotes As String 'local copy
Private mvarqth As String 'local copy
Private mvarsta As String 'local copy
Private mvarcnt As String 'local copy
Friend Property Let cnt(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.cnt = 5
    mvarcnt = vData
End Property


Friend Property Get cnt() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.cnt
    cnt = mvarcnt
End Property



Friend Property Let sta(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.sta = 5
    mvarsta = vData
End Property


Friend Property Get sta() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.sta
    sta = mvarsta
End Property



Friend Property Let qth(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.qth = 5
    mvarqth = vData
End Property


Friend Property Get qth() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.qth
    qth = mvarqth
End Property



Friend Property Let notes(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.notes = 5
    mvarnotes = vData
End Property


Friend Property Get notes() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.notes
    notes = mvarnotes
End Property



Friend Property Let lon(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.lon = 5
    mvarlon = vData
End Property


Friend Property Get lon() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.lon
    lon = mvarlon
End Property



Friend Property Let lat(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.lat = 5
    mvarlat = vData
End Property


Friend Property Get lat() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.lat
    lat = mvarlat
End Property



Friend Property Let cal(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.cal = 5
    mvarcal = vData
End Property


Friend Property Get cal() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.cal
    cal = mvarcal
End Property



Friend Property Let cyc(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.cyc = 5
    mvarcyc = vData
End Property


Friend Property Get cyc() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.cyc
    cyc = mvarcyc
End Property



Friend Property Let daid(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.daid = 5
    mvardaid = vData
End Property


Friend Property Get daid() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.daid
    daid = mvardaid
End Property



Friend Property Let lsb(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.lsb = 5
    mvarlsb = vData
End Property


Friend Property Get lsb() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.lsb
    lsb = mvarlsb
End Property



Friend Property Let usb(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.usb = 5
    mvarusb = vData
End Property


Friend Property Get usb() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.usb
    usb = mvarusb
End Property



Public Property Let khz(ByVal vData As String)
'used when assigning a value to the property, on the left side of an assignment.
'Syntax: X.khz = 5
    mvarkhz = vData
End Property


Public Property Get khz() As String
'used when retrieving value of a property, on the right side of an assignment.
'Syntax: Debug.Print X.khz
    khz = mvarkhz
End Property



Private Sub Class_Initialize()

End Sub
