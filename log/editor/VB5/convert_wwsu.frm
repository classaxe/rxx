VERSION 5.00
Begin VB.Form frm_convert 
   BackColor       =   &H00FFC0FF&
   Caption         =   "Convert WWSU Coordinates"
   ClientHeight    =   1065
   ClientLeft      =   5085
   ClientTop       =   2865
   ClientWidth     =   5130
   Icon            =   "convert_wwsu.frx":0000
   LinkTopic       =   "Form2"
   ScaleHeight     =   1065
   ScaleWidth      =   5130
   Begin VB.CommandButton cmd_convert 
      Caption         =   "Convert"
      Height          =   285
      Left            =   4080
      TabIndex        =   5
      Top             =   120
      Width           =   975
   End
   Begin VB.TextBox edit_lat 
      Height          =   285
      Left            =   480
      TabIndex        =   1
      Top             =   120
      Width           =   1455
   End
   Begin VB.TextBox edit_lon 
      Height          =   285
      Left            =   2520
      TabIndex        =   0
      Top             =   120
      Width           =   1455
   End
   Begin VB.Label Label1 
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      Caption         =   $"convert_wwsu.frx":0442
      ForeColor       =   &H80000008&
      Height          =   495
      Left            =   120
      TabIndex        =   4
      Top             =   600
      Width           =   4935
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
      Left            =   2040
      TabIndex        =   3
      Top             =   165
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
      Left            =   0
      TabIndex        =   2
      Top             =   165
      Width           =   375
   End
End
Attribute VB_Name = "frm_convert"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub cmd_convert_Click()
  Dim sign As String, deg As Integer, min As Integer, sec As Integer, lat As String, lon As String
  Dim startPos As Integer, endPos As Integer, temp As String
  
  temp = edit_lat.Text
  startPos = 1
  endPos = InStr(startPos, temp, ".")
  deg = Val(Mid(temp, startPos, endPos - startPos))
  
  startPos = endPos + 1
  endPos = InStr(startPos, temp, ".")
  min = Val(Mid(temp, startPos, endPos - startPos))
  
  startPos = endPos + 1
  endPos = InStr(startPos, temp, ".")
  sec = Val(Mid(temp, startPos, endPos - startPos))
  
  startPos = endPos + 1
  sign = Right(temp, 1)
  
  If (sign <> "N" And sign <> "S") Then
    MsgBox "Sorry - this doesn't look like a WWSU type latitude.", vbExclamation + vbOKOnly, "Error"
    Exit Sub
  End If
  lat = deg + (CInt(((Val(min) / 60) + (Val(sec / 3600))) * 10000) / 10000)
  If (sign = "S") Then
    lat = lat * -1
  End If
  
  temp = edit_lon.Text
  startPos = 1
  endPos = InStr(startPos, temp, ".")
  deg = Val(Mid(temp, startPos, endPos - startPos))
  
  startPos = endPos + 1
  endPos = InStr(startPos, temp, ".")
  min = Val(Mid(temp, startPos, endPos - startPos))
  
  startPos = endPos + 1
  endPos = InStr(startPos, temp, ".")
  sec = Val(Mid(temp, startPos, endPos - startPos))
  
  startPos = endPos + 1
  sign = Right(temp, 1)
  
  If (sign <> "E" And sign <> "W") Then
    MsgBox "Sorry - this doesn't look like a WWSU type longitude.", vbExclamation + vbOKOnly, "Error"
    Exit Sub
  End If
  lon = deg + (CInt(((Val(min) / 60) + (Val(sec / 3600))) * 10000) / 10000)
  If (sign = "W") Then
    lon = lon * -1
  End If

  frm_stations.edit_lat.Text = lat
  frm_stations.edit_lon.Text = lon
End Sub
