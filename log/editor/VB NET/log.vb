Option Strict Off
Option Explicit On
Module Types
	Public Structure STATION
		Dim khz As String
		Dim cal As String
		Dim qth As String
		Dim sta As String
		Dim cnt As String
		Dim cyc As String
		Dim daid As String
		Dim lsb As String
		Dim usb As String
		Dim pwr As String
		Dim lat As String
		Dim lon As String
		Dim notes As String
	End Structure
	
	Public Structure BROWSEINFO
		Dim hOwner As Integer
		Dim pidlRoot As Integer
		Dim pszDisplayName As String
		Dim lpszTitle As String
		Dim ulFlags As Integer
		Dim lpfn As Integer
		Dim lParam As Integer
		Dim iImage As Integer
	End Structure
	
	Public Const BIF_RETURNONLYFSDIRS As Short = &H1s
	Public Const BIF_DONTGOBELOWDOMAIN As Short = &H2s
	Public Const BIF_STATUSTEXT As Short = &H4s
	Public Const BIF_RETURNFSANCESTORS As Short = &H8s
	Public Const BIF_BROWSEFORCOMPUTER As Short = &H1000s
	Public Const BIF_BROWSEFORPRINTER As Short = &H2000s
	Public Const MAX_PATH As Short = 260
	
	Public Declare Function SHGetPathFromIDList Lib "shell32"  Alias "SHGetPathFromIDListA"(ByVal pidl As Integer, ByVal pszPath As String) As Integer
	
	'UPGRADE_WARNING: Structure BROWSEINFO may require marshalling attributes to be passed as an argument in this Declare statement. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1050"'
	Public Declare Function SHBrowseForFolder Lib "shell32"  Alias "SHBrowseForFolderA"(ByRef lpBrowseInfo As BROWSEINFO) As Integer
	
	'UPGRADE_NOTE: pv was upgraded to pv_Renamed. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1061"'
	Public Declare Sub CoTaskMemFree Lib "ole32" (ByVal pv_Renamed As Integer)
End Module