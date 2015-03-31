VERSION 5.00
Begin VB.Form frm_stations 
   BackColor       =   &H0080FFFF&
   Caption         =   "NDB Weblog Station Editor"
   ClientHeight    =   2880
   ClientLeft      =   60
   ClientTop       =   345
   ClientWidth     =   4560
   Icon            =   "stations.frx":0000
   LinkTopic       =   "Form1"
   ScaleHeight     =   2880
   ScaleWidth      =   4560
   StartUpPosition =   3  'Windows Default
   Begin VB.CommandButton cmd_about 
      BackColor       =   &H00C0FFFF&
      DisabledPicture =   "stations.frx":014A
      Height          =   375
      Left            =   4200
      Picture         =   "stations.frx":019F
      Style           =   1  'Graphical
      TabIndex        =   21
      ToolTipText     =   "Help Information"
      Top             =   0
      Width           =   375
   End
   Begin VB.CommandButton cmd_delete 
      BackColor       =   &H00C0FFFF&
      DisabledPicture =   "stations.frx":01F4
      Height          =   375
      Left            =   2160
      Picture         =   "stations.frx":0232
      Style           =   1  'Graphical
      TabIndex        =   20
      ToolTipText     =   "Delete this record"
      Top             =   0
      Width           =   375
   End
   Begin VB.CommandButton cmd_add 
      BackColor       =   &H00C0FFFF&
      DisabledPicture =   "stations.frx":0270
      Height          =   375
      Left            =   1800
      Picture         =   "stations.frx":02B2
      Style           =   1  'Graphical
      TabIndex        =   19
      ToolTipText     =   "Add a new record after this one"
      Top             =   0
      Width           =   375
   End
   Begin VB.CommandButton cmd_save 
      BackColor       =   &H00C0FFFF&
      DisabledPicture =   "stations.frx":02F4
      Height          =   375
      Left            =   0
      Picture         =   "stations.frx":0383
      Style           =   1  'Graphical
      TabIndex        =   16
      ToolTipText     =   "Save Changes to stations.js"
      Top             =   0
      Width           =   375
   End
   Begin VB.CommandButton cmd_undo 
      BackColor       =   &H00C0FFFF&
      DisabledPicture =   "stations.frx":040F
      Height          =   375
      Left            =   1320
      Picture         =   "stations.frx":0455
      Style           =   1  'Graphical
      TabIndex        =   18
      ToolTipText     =   "Undo changes to record"
      Top             =   0
      Width           =   375
   End
   Begin VB.CommandButton cmd_apply 
      BackColor       =   &H00C0FFFF&
      DisabledPicture =   "stations.frx":049B
      Height          =   375
      Left            =   960
      Picture         =   "stations.frx":051A
      Style           =   1  'Graphical
      TabIndex        =   17
      ToolTipText     =   "Accept changes to record"
      Top             =   0
      Width           =   375
   End
   Begin VB.HScrollBar scroll_station 
      Height          =   255
      LargeChange     =   10
      Left            =   720
      TabIndex        =   15
      Top             =   2520
      Width           =   3615
   End
   Begin VB.CheckBox chk_edit 
      BackColor       =   &H0080FFFF&
      Height          =   255
      Left            =   480
      TabIndex        =   14
      Top             =   2520
      Width           =   255
   End
   Begin VB.Frame fra_station_edit 
      BackColor       =   &H0080FFFF&
      Caption         =   "Station n of m"
      Height          =   1935
      Left            =   0
      TabIndex        =   0
      Top             =   480
      Width           =   4575
      Begin VB.ListBox edit_daid 
         Height          =   255
         ItemData        =   "stations.frx":0599
         Left            =   1320
         List            =   "stations.frx":05A6
         TabIndex        =   3
         Top             =   480
         Width           =   495
      End
      Begin VB.TextBox edit_notes 
         Height          =   285
         Left            =   720
         TabIndex        =   13
         Top             =   1560
         Width           =   3495
      End
      Begin VB.TextBox edit_lon 
         Height          =   285
         Left            =   2760
         TabIndex        =   12
         Top             =   1200
         Width           =   1455
      End
      Begin VB.TextBox edit_lat 
         Height          =   285
         Left            =   720
         TabIndex        =   11
         Top             =   1200
         Width           =   1455
      End
      Begin VB.TextBox edit_sta 
         Height          =   285
         Left            =   3120
         TabIndex        =   9
         Top             =   840
         Width           =   495
      End
      Begin VB.TextBox edit_cnt 
         Height          =   285
         Left            =   3720
         TabIndex        =   10
         Top             =   840
         Width           =   495
      End
      Begin VB.TextBox edit_qth 
         Height          =   285
         Left            =   720
         TabIndex        =   8
         Top             =   840
         Width           =   2295
      End
      Begin VB.TextBox edit_pwr 
         Height          =   285
         Left            =   3720
         TabIndex        =   7
         Top             =   480
         Width           =   495
      End
      Begin VB.TextBox edit_khz 
         Height          =   285
         Left            =   120
         TabIndex        =   1
         Top             =   480
         Width           =   495
      End
      Begin VB.TextBox edit_call 
         Height          =   285
         Left            =   720
         TabIndex        =   2
         Top             =   480
         Width           =   495
      End
      Begin VB.TextBox edit_cyc 
         Height          =   285
         Left            =   1920
         TabIndex        =   4
         Top             =   480
         Width           =   495
      End
      Begin VB.TextBox edit_lsb 
         Height          =   285
         Left            =   2520
         TabIndex        =   5
         Top             =   480
         Width           =   495
      End
      Begin VB.TextBox edit_usb 
         Height          =   285
         Left            =   3120
         TabIndex        =   6
         Top             =   480
         Width           =   495
      End
      Begin VB.Label Label22 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "Notes"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   120
         TabIndex        =   56
         Top             =   1600
         Width           =   495
      End
      Begin VB.Label Label21 
         BackStyle       =   0  'Transparent
         Caption         =   "USB"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   3120
         TabIndex        =   44
         Top             =   240
         Width           =   420
      End
      Begin VB.Label Label20 
         BackStyle       =   0  'Transparent
         Caption         =   "LSB"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   2520
         TabIndex        =   43
         Top             =   240
         Width           =   420
      End
      Begin VB.Label Label19 
         BackStyle       =   0  'Transparent
         Caption         =   "Cycle"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   1920
         TabIndex        =   42
         Top             =   240
         Width           =   540
      End
      Begin VB.Label Label18 
         BackStyle       =   0  'Transparent
         Caption         =   "DAID"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   1320
         TabIndex        =   41
         Top             =   240
         Width           =   420
      End
      Begin VB.Label Label17 
         BackStyle       =   0  'Transparent
         Caption         =   "Call"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   720
         TabIndex        =   40
         Top             =   240
         Width           =   420
      End
      Begin VB.Label Label16 
         BackStyle       =   0  'Transparent
         Caption         =   "KHz"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   39
         Top             =   240
         Width           =   375
      End
      Begin VB.Label Label15 
         BackStyle       =   0  'Transparent
         Caption         =   "Pwr(W)"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   3720
         TabIndex        =   38
         Top             =   240
         Width           =   660
      End
      Begin VB.Label Label14 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "QTH"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   37
         Top             =   885
         Width           =   375
      End
      Begin VB.Label Label13 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "Lat"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   36
         Top             =   1245
         Width           =   375
      End
      Begin VB.Label Label12 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "Lon"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   2280
         TabIndex        =   35
         Top             =   1245
         Width           =   375
      End
   End
   Begin VB.Frame fra_station_show 
      BackColor       =   &H0080FFFF&
      Caption         =   "Station n of m"
      Height          =   1935
      Left            =   0
      TabIndex        =   23
      Top             =   480
      Visible         =   0   'False
      Width           =   4575
      Begin VB.Label show_notes 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   765
         TabIndex        =   58
         Top             =   1605
         Width           =   3420
      End
      Begin VB.Label Label23 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "Notes"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   120
         TabIndex        =   57
         Top             =   1600
         Width           =   495
      End
      Begin VB.Label show_lon 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   2805
         TabIndex        =   55
         Top             =   1245
         Width           =   1380
      End
      Begin VB.Label show_lat 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   765
         TabIndex        =   54
         Top             =   1245
         Width           =   1335
      End
      Begin VB.Label show_cnt 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   3765
         TabIndex        =   53
         Top             =   885
         Width           =   375
      End
      Begin VB.Label show_sta 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   3165
         TabIndex        =   52
         Top             =   885
         Width           =   375
      End
      Begin VB.Label show_qth 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   765
         TabIndex        =   51
         Top             =   885
         Width           =   2175
      End
      Begin VB.Label show_pwr 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   3765
         TabIndex        =   50
         Top             =   525
         Width           =   375
      End
      Begin VB.Label show_usb 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   3165
         TabIndex        =   49
         Top             =   525
         Width           =   375
      End
      Begin VB.Label show_lsb 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   2565
         TabIndex        =   48
         Top             =   525
         Width           =   375
      End
      Begin VB.Label show_cyc 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   1965
         TabIndex        =   47
         Top             =   525
         Width           =   375
      End
      Begin VB.Label show_daid 
         BackColor       =   &H00FFFFFF&
         Caption         =   "?"
         Height          =   255
         Left            =   1365
         TabIndex        =   46
         Top             =   525
         Width           =   135
      End
      Begin VB.Label show_call 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   765
         TabIndex        =   45
         Top             =   525
         Width           =   375
      End
      Begin VB.Label Label11 
         BackStyle       =   0  'Transparent
         Caption         =   "USB"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   3120
         TabIndex        =   34
         Top             =   240
         Width           =   420
      End
      Begin VB.Label Label6 
         BackStyle       =   0  'Transparent
         Caption         =   "LSB"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   2520
         TabIndex        =   33
         Top             =   240
         Width           =   420
      End
      Begin VB.Label Label5 
         BackStyle       =   0  'Transparent
         Caption         =   "Cycle"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   1920
         TabIndex        =   32
         Top             =   240
         Width           =   540
      End
      Begin VB.Label Label4 
         BackStyle       =   0  'Transparent
         Caption         =   "DAID"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   1320
         TabIndex        =   31
         Top             =   240
         Width           =   420
      End
      Begin VB.Label Label3 
         BackStyle       =   0  'Transparent
         Caption         =   "Call"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   720
         TabIndex        =   30
         Top             =   240
         Width           =   300
      End
      Begin VB.Label label2 
         BackStyle       =   0  'Transparent
         Caption         =   "KHz"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   29
         Top             =   240
         Width           =   375
      End
      Begin VB.Label Label7 
         BackStyle       =   0  'Transparent
         Caption         =   "Pwr(W)"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   3720
         TabIndex        =   28
         Top             =   240
         Width           =   660
      End
      Begin VB.Label Label8 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "QTH"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   27
         Top             =   885
         Width           =   375
      End
      Begin VB.Label Label9 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "Lat"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   240
         TabIndex        =   26
         Top             =   1245
         Width           =   375
      End
      Begin VB.Label Label10 
         Alignment       =   1  'Right Justify
         BackStyle       =   0  'Transparent
         Caption         =   "Lon"
         BeginProperty Font 
            Name            =   "Arial"
            Size            =   8.25
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   2280
         TabIndex        =   25
         Top             =   1245
         Width           =   375
      End
      Begin VB.Label show_khz 
         BackColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   160
         TabIndex        =   22
         Top             =   525
         Width           =   495
      End
   End
   Begin VB.Shape Shape1 
      BackColor       =   &H0000FFFF&
      BackStyle       =   1  'Opaque
      Height          =   375
      Left            =   0
      Top             =   0
      Width           =   4575
   End
   Begin VB.Label Label1 
      BackColor       =   &H8000000E&
      BackStyle       =   0  'Transparent
      Caption         =   "Edit"
      BeginProperty Font 
         Name            =   "Arial"
         Size            =   8.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   255
      Left            =   120
      TabIndex        =   24
      Top             =   2520
      Width           =   375
   End
End
Attribute VB_Name = "frm_stations"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Dim header As String, data_path As String, backup_file As String, data_file As String, save_alert_shown
Dim stations() As STATION, stationsCount As Integer, stationCurrent As Integer 'Array to hold all data
Dim editStatus As Boolean, recordChanged As Boolean, stationLoaded As Boolean, saveEnabled As Boolean
Private Sub Form_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub cmd_save_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub cmd_apply_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub cmd_undo_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub cmd_add_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub cmd_delete_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub cmd_about_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub scroll_station_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub chk_edit_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_khz_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_Call_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_daid_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_cyc_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_lsb_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_usb_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_pwr_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_qth_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_sta_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_cnt_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_lat_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_lon_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Private Sub edit_notes_KeyDown(KeyCode As Integer, Shift As Integer)
  Call KeyDown(KeyCode, Shift)
End Sub
Sub KeyDown(KeyCode As Integer, Shift As Integer)
  If ((Shift And vbCtrlMask) > 0 And KeyCode = vbKeyS) Then
    Call cmd_save_Click
  End If
End Sub


Sub Form_Load()
  Dim i As Integer
  frm_stations.Caption = get_name()
  frm_stations.Height = 3400
  editStatus = False
  saveEnabled = False
  recordChanged = False
  stationLoaded = False
  stationCurrent = 0
  backup_file = "stations.bak"
  data_file = "stations.js"
  If (data_path = "") Then
    If (Command() <> "") Then
      data_path = Command()
    Else
      data_path = App.path
    End If
  End If
  save_alert_shown = 0
  Call load_stations
  Call update_station
  Call update_status
  scroll_station.Max = stationsCount
  stationLoaded = True
End Sub

Private Sub cmd_about_Click()
  frm_about.Visible = True
End Sub

Sub get_path()
  Dim bi As BROWSEINFO
  Dim pidl As Long
  Dim path As String
  Dim pos As Integer
  bi.hOwner = Me.hWnd
  bi.pidlRoot = 0&
  bi.lpszTitle = "Select the directory where your " & data_file & " file resides"
  bi.ulFlags = BIF_RETURNONLYFSDIRS
  pidl = SHBrowseForFolder(bi)
  path = Space$(MAX_PATH)
  If SHGetPathFromIDList(ByVal pidl, ByVal path) Then
     pos = InStr(path, Chr$(0))
     data_path = Left(path, pos - 1)
  End If
  Call CoTaskMemFree(pidl)
End Sub

Sub add_station()
  Dim i As Integer
  Dim new_station As STATION
  new_station.daid = "Y"
  stationsCount = stationsCount + 1
  ReDim Preserve stations(stationsCount)
  For i = stationsCount - 1 To stationCurrent Step -1
    stations(i + 1) = stations(i)
  Next
  
  stationCurrent = stationCurrent + 1
  
  stations(stationCurrent) = new_station
  stations(stationCurrent).daid = "?"
  scroll_station.Max = stationsCount
  scroll_station.Value = stationCurrent
  saveEnabled = True
  Call scroll_station_Change
  Call update_status
  Call update_station
End Sub
Sub delete_station()
  Dim i As Integer
  If (stationsCount > 0) Then
    saveEnabled = True
    Dim temp_array() As STATION
    Dim new_index As Integer
  
    ReDim temp_array(stationsCount)
    new_index = 0
  
    For i = 0 To stationsCount
      If (i <> stationCurrent) Then
        temp_array(new_index) = stations(i)
        new_index = new_index + 1
      End If
    Next
    stationsCount = stationsCount - 1
    ReDim stations(stationsCount)
    For i = 0 To stationsCount
      stations(i) = temp_array(i)
    Next
    If (stationCurrent > stationsCount) Then
      stationCurrent = stationsCount
    End If
    scroll_station.Max = stationsCount
    scroll_station.Value = stationCurrent
    Call scroll_station_Change
    Call update_status
    Call update_station
  End If
End Sub
Private Sub chk_edit_Click()
  editStatus = Not editStatus
  Call update_station
  Call update_status
End Sub
Private Sub cmd_add_Click()
  If (recordChanged) Then
    Call cmd_apply_Click
  End If
  Call add_station
End Sub
Private Sub cmd_delete_Click()
  Call delete_station
End Sub
Private Sub cmd_save_Click()
  Call update_coords
  Call update_record
  Call edit
  Call save_stations
  Call update_status
End Sub
Private Sub cmd_undo_Click()
  Call update_station
End Sub
Private Sub edit_khz_Change()
  Call edit
End Sub
Private Sub edit_call_Change()
  Call edit
End Sub
Private Sub edit_daid_Click()
  Call edit
End Sub
Private Sub edit_cyc_Change()
  Call edit
End Sub
Private Sub edit_lsb_Change()
  Call edit
End Sub
Private Sub edit_usb_Change()
  Call edit
End Sub
Private Sub edit_pwr_Change()
  Call edit
End Sub
Private Sub edit_qth_Change()
  Call edit
End Sub
Private Sub edit_sta_Change()
  Call edit
End Sub
Private Sub edit_cnt_Change()
  Call edit
End Sub
Private Sub edit_lat_Change()
  Call edit
End Sub
Private Sub edit_lon_Change()
  Call edit
End Sub
Private Sub edit_notes_Change()
  Call edit
End Sub
Private Sub cmd_apply_Click()
  Call update_coords
  Call update_record
  Call update_status
  Call edit
End Sub
Sub update_record()
  Dim temp, i
  edit_call.Text = UCase(edit_call.Text)
  edit_sta.Text = UCase(edit_sta.Text)
  edit_cnt.Text = UCase(edit_cnt.Text)
  
  If (edit_qth.Text <> "") Then
    temp = Trim(LCase(edit_qth.Text)) ' Convert the title only to lower case
    Mid(temp, 1, 1) = UCase(Mid(temp, 1, 1))
    For i = 2 To Len(temp) Step 1
      If Mid(temp, i, 1) = " " Then
        Mid(temp, i + 1, 1) = UCase(Mid(temp, i + 1, 1))
      End If
    Next i
    edit_qth.Text = temp
  End If
  
  stations(stationCurrent).khz = edit_khz.Text
  stations(stationCurrent).cal = edit_call.Text
  stations(stationCurrent).daid = edit_daid.Text
  stations(stationCurrent).cyc = edit_cyc.Text
  stations(stationCurrent).lsb = edit_lsb.Text
  stations(stationCurrent).usb = edit_usb.Text
  stations(stationCurrent).pwr = edit_pwr.Text
  stations(stationCurrent).qth = edit_qth.Text
  stations(stationCurrent).sta = edit_sta.Text
  stations(stationCurrent).cnt = edit_cnt.Text
  stations(stationCurrent).lat = edit_lat.Text
  stations(stationCurrent).lon = edit_lon.Text
  stations(stationCurrent).notes = edit_notes.Text
  saveEnabled = True
End Sub

Sub update_status()
  If (editStatus) Then
    fra_station_edit.Visible = True
    fra_station_show.Visible = False
    If (recordChanged) Then
      cmd_apply.Enabled = True
      cmd_undo.Enabled = True
    End If
    cmd_add.Enabled = True
    If (stationsCount > 0) Then
      cmd_delete.Enabled = True
    Else
      cmd_delete.Enabled = False
    End If
  Else
    fra_station_edit.Visible = False
    fra_station_show.Visible = True
    cmd_apply.Enabled = False
    cmd_undo.Enabled = False
    cmd_add.Enabled = False
    cmd_delete.Enabled = False
    frm_stations.Height = 3400
  End If
  cmd_save.Enabled = saveEnabled
End Sub
Sub edit()
  recordChanged = False
  If (stationLoaded) Then
    If (edit_khz.Text <> stations(stationCurrent).khz) Then
      recordChanged = True
    End If
    If (edit_call.Text <> stations(stationCurrent).cal) Then
      recordChanged = True
    End If
    If (edit_daid.Text <> stations(stationCurrent).daid) Then
      recordChanged = True
    End If
    If (edit_cyc.Text <> stations(stationCurrent).cyc) Then
      recordChanged = True
    End If
    If (edit_lsb.Text <> stations(stationCurrent).lsb) Then
      recordChanged = True
    End If
    If (edit_usb.Text <> stations(stationCurrent).usb) Then
      recordChanged = True
    End If
    If (edit_pwr.Text <> stations(stationCurrent).pwr) Then
      recordChanged = True
    End If
    If (edit_qth.Text <> stations(stationCurrent).qth) Then
      recordChanged = True
    End If
    If (edit_sta.Text <> stations(stationCurrent).sta) Then
      recordChanged = True
    End If
    If (edit_cnt.Text <> stations(stationCurrent).cnt) Then
      recordChanged = True
    End If
    If (edit_lat.Text <> stations(stationCurrent).lat) Then
      recordChanged = True
    End If
    If (edit_lon.Text <> stations(stationCurrent).lon) Then
      recordChanged = True
    End If
    If (edit_notes.Text <> stations(stationCurrent).notes) Then
      recordChanged = True
    End If
    If (recordChanged) Then
      cmd_apply.Enabled = True
      cmd_undo.Enabled = True
      cmd_save.Enabled = True
    Else
      cmd_apply.Enabled = False
      cmd_undo.Enabled = False
    End If
  End If
End Sub
Sub save_stations()
  Dim i As Integer
  Dim sep As String, a As STATION
  On Error Resume Next

  sep = Chr$(34) & "," & Chr$(34)

  Kill data_path & "\" & backup_file
  If (Err.Number <> 0 And Err.Number <> 53) Then 'This isn't file not found
    MsgBox Err.Description
  End If
  
  If (Err.Number <> 53) Then
    If (save_alert_shown <> 1) Then
      MsgBox "Old " & data_file & " file has been renamed to " & backup_file & "." & Chr$(13) & "From now on, you won't see this message.", vbOKOnly + vbInformation, get_name()
      save_alert_shown = 1
    End If
  End If
  
  Name data_path & "\" & data_file As data_path & "\" & backup_file
  
    
  Open data_path & "\" & data_file For Output As #1 ' Open file.
  Print #1, header
  For i = 0 To stationsCount
    a = stations(i)
    Print #1, "STATION (" & Chr$(34) & a.khz & sep & a.cal & sep & a.qth & sep & a.sta & sep & a.cnt & sep & a.cyc & sep & a.daid & sep & a.lsb & sep & a.usb & sep & a.pwr & sep & a.lat & sep & a.lon & sep & a.notes & Chr$(34) & ");"
  Next
  Close #1
  saveEnabled = False
End Sub

Sub load_stations()
  Dim temp As String, tempStation As STATION, startPos As Integer, endPos As Integer
  Dim stationsSize As Integer, FileNumber As Integer, response As Integer
  stationsSize = 50
  header = ""
  On Error Resume Next
  FileNumber = FreeFile
  
  Open data_path & "\" & data_file For Input As FileNumber ' Open file.
  
  If Err.Number = 53 Then
    MsgBox "The " & data_file & " file isn't in the" & Chr$(13) & data_path & " folder.", vbExclamation + vbOKOnly, get_name()
  End If
  
  Do While (Err.Number = 53)
    Err.Clear
    response = MsgBox("Please specify where the " & data_file & " file you wish to edit is located," & Chr$(13) & "or press cancel to quit", vbExclamation + vbOKCancel, get_name())
    If (response = vbCancel) Then
      MsgBox "Quitting NDB Weblog Station Editor", vbInformation + vbOKOnly, get_name()
      End
    End If
    Call get_path
    Open data_path & "\" & data_file For Input As FileNumber ' Open file.
  Loop
  
  
  
  ReDim stations(stationsSize)
  Do While Not EOF(FileNumber)
    Line Input #FileNumber, temp  ' Get each line contents.
    If (Left(temp, 7) = "STATION") Then
      ' Ensure that stations() is big enough to handle all stations
      If (stationsCount > stationsSize) Then
        stationsSize = stationsSize + 50
        ReDim Preserve stations(stationsSize)
      End If
      Err
      startPos = InStr(1, temp, Chr$(34)) + 1
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.khz = Mid(temp, startPos, endPos - startPos)

      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.cal = Mid(temp, startPos, endPos - startPos)
      
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.qth = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.sta = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.cnt = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.cyc = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.daid = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.lsb = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.usb = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.pwr = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.lat = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.lon = Mid(temp, startPos, endPos - startPos)
    
      startPos = endPos + 3
      endPos = InStr(startPos, temp, Chr$(34))
      tempStation.notes = Mid(temp, startPos, endPos - startPos)
    
      stations(stationsCount) = tempStation
      stationsCount = stationsCount + 1
   
    'Debug.Print temp  ' Print to Debug window.
    Else
      header = header & temp & Chr$(13)
    End If
  Loop
  stationsCount = stationsCount - 1
  header = Left(header, Len(header) - 1)
  Close #FileNumber    ' Close file.
End Sub
Sub update_station()
  recordChanged = False
  stationLoaded = False
  edit_khz.Text = stations(stationCurrent).khz
  edit_call.Text = stations(stationCurrent).cal
  edit_daid.Text = stations(stationCurrent).daid
  edit_cyc.Text = stations(stationCurrent).cyc
  edit_lsb.Text = stations(stationCurrent).lsb
  edit_usb.Text = stations(stationCurrent).usb
  edit_pwr.Text = stations(stationCurrent).pwr
  edit_qth.Text = stations(stationCurrent).qth
  edit_sta.Text = stations(stationCurrent).sta
  edit_cnt.Text = stations(stationCurrent).cnt
  edit_lat.Text = stations(stationCurrent).lat
  edit_lon.Text = stations(stationCurrent).lon
  edit_notes.Text = stations(stationCurrent).notes
  fra_station_edit.Caption = "Station " & (stationCurrent + 1) & " of " & (stationsCount + 1) & " (Edit Mode)"
  show_khz.Caption = stations(stationCurrent).khz
  show_call.Caption = stations(stationCurrent).cal
  show_daid.Caption = stations(stationCurrent).daid
  show_cyc.Caption = stations(stationCurrent).cyc
  show_lsb.Caption = stations(stationCurrent).lsb
  show_usb.Caption = stations(stationCurrent).usb
  show_pwr.Caption = stations(stationCurrent).pwr
  show_qth.Caption = stations(stationCurrent).qth
  show_sta.Caption = stations(stationCurrent).sta
  show_cnt.Caption = stations(stationCurrent).cnt
  show_lat.Caption = stations(stationCurrent).lat
  show_lon.Caption = stations(stationCurrent).lon
  show_notes.Caption = stations(stationCurrent).notes
  fra_station_show.Caption = "Station " & (stationCurrent + 1) & " of " & (stationsCount + 1) & " (Display Mode)"
  stationLoaded = True
  Call edit
End Sub

Private Sub scroll_station_Change()
  stationCurrent = scroll_station.Value
  Call update_station
  Call update_status
End Sub
Private Sub scroll_station_Scroll()
  stationCurrent = scroll_station.Value
  Call update_station
  Call update_status
End Sub
Sub update_coords()
  Dim sign As String, deg As Integer, min As Integer, sec As Integer, lat As String, lon As String
  Dim startPos As Integer, endPos As Integer, temp As String
  
  ' Has any value been given?
  If (edit_lat.Text & edit_lon.Text) = "" Then
    Exit Sub
  End If
  
  'See if user swapped values
  If ((Right(edit_lat.Text, 1) = "E" Or Right(edit_lat.Text, 1) = "W")) Then
    Call lat_error
    Exit Sub
  End If
  If ((Right(edit_lon.Text, 1) = "N" Or Right(edit_lon.Text, 1) = "S")) Then
    Call lon_error
    Exit Sub
  End If
    
  ' See if these are WWSU coordinates
  If ((Right(edit_lat.Text, 1) = "N" Or Right(edit_lat.Text, 1) = "S") And (Right(edit_lon.Text, 1) = "E" Or Right(edit_lon.Text, 1) = "W")) Then
    temp = edit_lat.Text
    startPos = 1
    endPos = InStr(startPos, temp, ".")
    If (endPos = 0) Then
      Call lat_error
      Exit Sub
    End If
    deg = Val(Mid(temp, startPos, endPos - startPos))
  
    startPos = endPos + 1
    endPos = InStr(startPos, temp, ".")
    If (endPos = 0) Then
      Call lat_error
      Exit Sub
    End If
    min = Val(Mid(temp, startPos, endPos - startPos))
  
    startPos = endPos + 1
    endPos = InStr(startPos, temp, ".")
    If (endPos = 0) Then
      Call lat_error
      Exit Sub
    End If
    sec = Val(Mid(temp, startPos, endPos - startPos))
  
    startPos = endPos + 1
    sign = Right(temp, 1)
  
    If (sign <> "N" And sign <> "S") Then
      Call lat_error
      Exit Sub
    End If
    lat = deg + (CInt(((Val(min) / 60) + (Val(sec / 3600))) * 10000) / 10000)
    If (sign = "S") Then
      lat = lat * -1
    End If
  
    temp = edit_lon.Text
    startPos = 1
    endPos = InStr(startPos, temp, ".")
    If (endPos = 0) Then
      Call lon_error
      Exit Sub
    End If
    deg = Val(Mid(temp, startPos, endPos - startPos))
  
    startPos = endPos + 1
    endPos = InStr(startPos, temp, ".")
    If (endPos = 0) Then
      Call lon_error
      Exit Sub
    End If
    min = Val(Mid(temp, startPos, endPos - startPos))
  
    startPos = endPos + 1
    endPos = InStr(startPos, temp, ".")
    If (endPos = 0) Then
      Call lon_error
      Exit Sub
    End If
    sec = Val(Mid(temp, startPos, endPos - startPos))
  
    startPos = endPos + 1
    sign = Right(temp, 1)
  
    If (sign <> "E" And sign <> "W") Then
      Call lon_error
      Exit Sub
    End If
    lon = deg + (CInt(((Val(min) / 60) + (Val(sec / 3600))) * 10000) / 10000)
    If (sign = "W") Then
      lon = lon * -1
    End If

    edit_lat.Text = lat
    edit_lon.Text = lon
  Else
    If (Val(edit_lat.Text) = 0) Then
      Call lat_error
      Exit Sub
    Else
      edit_lat.Text = Val(edit_lat.Text)
    End If
    If (Val(edit_lon.Text) = 0) Then
      Call lon_error
      Exit Sub
    Else
      edit_lon.Text = Val(edit_lon.Text)
    End If
  End If
End Sub
Sub lat_error()
  MsgBox "Sorry - I can't understand this latitude." & Chr$(13) & "Acceptable formats are Decimal or WWSU latitude:" & Chr$(13) & "  DD.NNNN or" & Chr$(13) & "  DD.MM.SS.H (where H is N or S).", vbExclamation + vbOKOnly, get_name()
  edit_lat.Text = ""
End Sub
Sub lon_error()
  MsgBox "Sorry - I can't understand this longitude." & Chr$(13) & "Acceptable formats are Decimal or WWSU longitude:" & Chr$(13) & "  DD.NNNN or" & Chr$(13) & "  DDD.MM.SS.H (where H is E or W).", vbExclamation + vbOKOnly, get_name()
  edit_lon.Text = ""
End Sub

