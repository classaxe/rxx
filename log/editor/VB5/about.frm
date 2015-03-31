VERSION 5.00
Begin VB.Form frm_about 
   BackColor       =   &H00A0FFA0&
   Caption         =   "About..."
   ClientHeight    =   4125
   ClientLeft      =   3765
   ClientTop       =   4050
   ClientWidth     =   6240
   Icon            =   "about.frx":0000
   LinkTopic       =   "Form1"
   ScaleHeight     =   4125
   ScaleWidth      =   6240
   Begin VB.CommandButton cmd_ok 
      Caption         =   "OK"
      Height          =   375
      Left            =   2280
      TabIndex        =   6
      Top             =   3720
      Width           =   1455
   End
   Begin VB.Frame Frame2 
      BackColor       =   &H00A0FFA0&
      Caption         =   "WWSU NDB Database"
      Height          =   1335
      Left            =   120
      TabIndex        =   4
      Top             =   2280
      Width           =   6015
      Begin VB.Label Label4 
         Appearance      =   0  'Flat
         BackColor       =   &H80000005&
         BackStyle       =   0  'Transparent
         Caption         =   "Data from WWSU may be pasted directly into the fields of this tool."
         ForeColor       =   &H80000008&
         Height          =   300
         Left            =   240
         TabIndex        =   7
         Top             =   360
         Width           =   5535
      End
      Begin VB.Label Label3 
         Appearance      =   0  'Flat
         BackColor       =   &H80000005&
         BackStyle       =   0  'Transparent
         Caption         =   $"about.frx":014A
         ForeColor       =   &H80000008&
         Height          =   660
         Left            =   240
         TabIndex        =   5
         Top             =   645
         Width           =   5535
      End
   End
   Begin VB.CommandButton Command1 
      Caption         =   "OK"
      Height          =   375
      Left            =   2280
      TabIndex        =   3
      Top             =   2400
      Width           =   1695
   End
   Begin VB.Frame Frame1 
      BackColor       =   &H00A0FFA0&
      Caption         =   "NDB Weblog Editor"
      Height          =   2175
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   6015
      Begin VB.Label Label5 
         Appearance      =   0  'Flat
         BackColor       =   &H80000005&
         BackStyle       =   0  'Transparent
         Caption         =   "You can include the file path in the command line when you call this program. Use Ctrl+S to save changes"
         ForeColor       =   &H80000008&
         Height          =   495
         Left            =   240
         TabIndex        =   8
         Top             =   1560
         Width           =   5535
      End
      Begin VB.Label Label2 
         Appearance      =   0  'Flat
         BackColor       =   &H80000005&
         BackStyle       =   0  'Transparent
         Caption         =   $"about.frx":01FD
         ForeColor       =   &H80000008&
         Height          =   735
         Left            =   240
         TabIndex        =   2
         Top             =   840
         Width           =   5535
      End
      Begin VB.Label Label1 
         Appearance      =   0  'Flat
         BackColor       =   &H80000005&
         BackStyle       =   0  'Transparent
         Caption         =   $"about.frx":02EE
         ForeColor       =   &H80000008&
         Height          =   495
         Left            =   240
         TabIndex        =   1
         Top             =   360
         Width           =   5535
      End
   End
End
Attribute VB_Name = "frm_about"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Private Sub cmd_ok_Click()
  frm_about.Visible = False
End Sub
Private Sub Form_Load()
  frm_about.Caption = get_name()
End Sub

