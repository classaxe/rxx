Option Strict Off
Option Explicit On
Imports VB = Microsoft.VisualBasic
Friend Class frm_stations
	Inherits System.Windows.Forms.Form
#Region "Windows Form Designer generated code "
	Public Sub New()
		MyBase.New()
		If m_vb6FormDefInstance Is Nothing Then
			If m_InitializingDefInstance Then
				m_vb6FormDefInstance = Me
			Else
				Try 
					'For the start-up form, the first instance created is the default instance.
					If System.Reflection.Assembly.GetExecutingAssembly.EntryPoint.DeclaringType Is Me.GetType Then
						m_vb6FormDefInstance = Me
					End If
				Catch
				End Try
			End If
		End If
		'This call is required by the Windows Form Designer.
		InitializeComponent()
	End Sub
	'Form overrides dispose to clean up the component list.
	Protected Overloads Overrides Sub Dispose(ByVal Disposing As Boolean)
		If Disposing Then
			If Not components Is Nothing Then
				components.Dispose()
			End If
		End If
		MyBase.Dispose(Disposing)
	End Sub
	'Required by the Windows Form Designer
	Private components As System.ComponentModel.IContainer
	Public ToolTip1 As System.Windows.Forms.ToolTip
	Public WithEvents cmd_about As System.Windows.Forms.Button
	Public WithEvents cmd_delete As System.Windows.Forms.Button
	Public WithEvents cmd_add As System.Windows.Forms.Button
	Public WithEvents cmd_save As System.Windows.Forms.Button
	Public WithEvents cmd_undo As System.Windows.Forms.Button
	Public WithEvents cmd_apply As System.Windows.Forms.Button
	Public WithEvents scroll_station As System.Windows.Forms.HScrollBar
	Public WithEvents chk_edit As System.Windows.Forms.CheckBox
	Public WithEvents edit_daid As System.Windows.Forms.ListBox
	Public WithEvents edit_notes As System.Windows.Forms.TextBox
	Public WithEvents edit_lon As System.Windows.Forms.TextBox
	Public WithEvents edit_lat As System.Windows.Forms.TextBox
	Public WithEvents edit_sta As System.Windows.Forms.TextBox
	Public WithEvents edit_cnt As System.Windows.Forms.TextBox
	Public WithEvents edit_qth As System.Windows.Forms.TextBox
	Public WithEvents edit_pwr As System.Windows.Forms.TextBox
	Public WithEvents edit_khz As System.Windows.Forms.TextBox
	Public WithEvents edit_call As System.Windows.Forms.TextBox
	Public WithEvents edit_cyc As System.Windows.Forms.TextBox
	Public WithEvents edit_lsb As System.Windows.Forms.TextBox
	Public WithEvents edit_usb As System.Windows.Forms.TextBox
	Public WithEvents Label22 As System.Windows.Forms.Label
	Public WithEvents Label21 As System.Windows.Forms.Label
	Public WithEvents Label20 As System.Windows.Forms.Label
	Public WithEvents Label19 As System.Windows.Forms.Label
	Public WithEvents Label18 As System.Windows.Forms.Label
	Public WithEvents Label17 As System.Windows.Forms.Label
	Public WithEvents Label16 As System.Windows.Forms.Label
	Public WithEvents Label15 As System.Windows.Forms.Label
	Public WithEvents Label14 As System.Windows.Forms.Label
	Public WithEvents Label13 As System.Windows.Forms.Label
	Public WithEvents Label12 As System.Windows.Forms.Label
	Public WithEvents fra_station_edit As System.Windows.Forms.GroupBox
	Public WithEvents show_notes As System.Windows.Forms.Label
	Public WithEvents Label23 As System.Windows.Forms.Label
	Public WithEvents show_lon As System.Windows.Forms.Label
	Public WithEvents show_lat As System.Windows.Forms.Label
	Public WithEvents show_cnt As System.Windows.Forms.Label
	Public WithEvents show_sta As System.Windows.Forms.Label
	Public WithEvents show_qth As System.Windows.Forms.Label
	Public WithEvents show_pwr As System.Windows.Forms.Label
	Public WithEvents show_usb As System.Windows.Forms.Label
	Public WithEvents show_lsb As System.Windows.Forms.Label
	Public WithEvents show_cyc As System.Windows.Forms.Label
	Public WithEvents show_daid As System.Windows.Forms.Label
	Public WithEvents show_call As System.Windows.Forms.Label
	Public WithEvents Label11 As System.Windows.Forms.Label
	Public WithEvents Label6 As System.Windows.Forms.Label
	Public WithEvents Label5 As System.Windows.Forms.Label
	Public WithEvents Label4 As System.Windows.Forms.Label
	Public WithEvents Label3 As System.Windows.Forms.Label
	Public WithEvents label2 As System.Windows.Forms.Label
	Public WithEvents Label7 As System.Windows.Forms.Label
	Public WithEvents Label8 As System.Windows.Forms.Label
	Public WithEvents Label9 As System.Windows.Forms.Label
	Public WithEvents Label10 As System.Windows.Forms.Label
	Public WithEvents show_khz As System.Windows.Forms.Label
	Public WithEvents fra_station_show As System.Windows.Forms.GroupBox
	Public WithEvents Shape1 As System.Windows.Forms.Label
	Public WithEvents Label1 As System.Windows.Forms.Label
	'NOTE: The following procedure is required by the Windows Form Designer
	'It can be modified using the Windows Form Designer.
	'Do not modify it using the code editor.
	<System.Diagnostics.DebuggerStepThrough()> Private Sub InitializeComponent()
    Me.components = New System.ComponentModel.Container
    Dim resources As System.Resources.ResourceManager = New System.Resources.ResourceManager(GetType(frm_stations))
    Me.ToolTip1 = New System.Windows.Forms.ToolTip(Me.components)
    Me.cmd_about = New System.Windows.Forms.Button
    Me.cmd_delete = New System.Windows.Forms.Button
    Me.cmd_add = New System.Windows.Forms.Button
    Me.cmd_save = New System.Windows.Forms.Button
    Me.cmd_undo = New System.Windows.Forms.Button
    Me.cmd_apply = New System.Windows.Forms.Button
    Me.scroll_station = New System.Windows.Forms.HScrollBar
    Me.chk_edit = New System.Windows.Forms.CheckBox
    Me.fra_station_edit = New System.Windows.Forms.GroupBox
    Me.edit_daid = New System.Windows.Forms.ListBox
    Me.edit_notes = New System.Windows.Forms.TextBox
    Me.edit_lon = New System.Windows.Forms.TextBox
    Me.edit_lat = New System.Windows.Forms.TextBox
    Me.edit_sta = New System.Windows.Forms.TextBox
    Me.edit_cnt = New System.Windows.Forms.TextBox
    Me.edit_qth = New System.Windows.Forms.TextBox
    Me.edit_pwr = New System.Windows.Forms.TextBox
    Me.edit_khz = New System.Windows.Forms.TextBox
    Me.edit_call = New System.Windows.Forms.TextBox
    Me.edit_cyc = New System.Windows.Forms.TextBox
    Me.edit_lsb = New System.Windows.Forms.TextBox
    Me.edit_usb = New System.Windows.Forms.TextBox
    Me.Label22 = New System.Windows.Forms.Label
    Me.Label21 = New System.Windows.Forms.Label
    Me.Label20 = New System.Windows.Forms.Label
    Me.Label19 = New System.Windows.Forms.Label
    Me.Label18 = New System.Windows.Forms.Label
    Me.Label17 = New System.Windows.Forms.Label
    Me.Label16 = New System.Windows.Forms.Label
    Me.Label15 = New System.Windows.Forms.Label
    Me.Label14 = New System.Windows.Forms.Label
    Me.Label13 = New System.Windows.Forms.Label
    Me.Label12 = New System.Windows.Forms.Label
    Me.fra_station_show = New System.Windows.Forms.GroupBox
    Me.show_notes = New System.Windows.Forms.Label
    Me.Label23 = New System.Windows.Forms.Label
    Me.show_lon = New System.Windows.Forms.Label
    Me.show_lat = New System.Windows.Forms.Label
    Me.show_cnt = New System.Windows.Forms.Label
    Me.show_sta = New System.Windows.Forms.Label
    Me.show_qth = New System.Windows.Forms.Label
    Me.show_pwr = New System.Windows.Forms.Label
    Me.show_usb = New System.Windows.Forms.Label
    Me.show_lsb = New System.Windows.Forms.Label
    Me.show_cyc = New System.Windows.Forms.Label
    Me.show_daid = New System.Windows.Forms.Label
    Me.show_call = New System.Windows.Forms.Label
    Me.Label11 = New System.Windows.Forms.Label
    Me.Label6 = New System.Windows.Forms.Label
    Me.Label5 = New System.Windows.Forms.Label
    Me.Label4 = New System.Windows.Forms.Label
    Me.Label3 = New System.Windows.Forms.Label
    Me.label2 = New System.Windows.Forms.Label
    Me.Label7 = New System.Windows.Forms.Label
    Me.Label8 = New System.Windows.Forms.Label
    Me.Label9 = New System.Windows.Forms.Label
    Me.Label10 = New System.Windows.Forms.Label
    Me.show_khz = New System.Windows.Forms.Label
    Me.Shape1 = New System.Windows.Forms.Label
    Me.Label1 = New System.Windows.Forms.Label
    Me.fra_station_edit.SuspendLayout()
    Me.fra_station_show.SuspendLayout()
    Me.SuspendLayout()
    '
    'cmd_about
    '
    Me.cmd_about.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(192, Byte))
    Me.cmd_about.Cursor = System.Windows.Forms.Cursors.Default
    Me.cmd_about.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.cmd_about.ForeColor = System.Drawing.SystemColors.ControlText
    Me.cmd_about.Image = CType(resources.GetObject("cmd_about.Image"), System.Drawing.Image)
    Me.cmd_about.Location = New System.Drawing.Point(280, 0)
    Me.cmd_about.Name = "cmd_about"
    Me.cmd_about.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.cmd_about.Size = New System.Drawing.Size(25, 25)
    Me.cmd_about.TabIndex = 21
    Me.cmd_about.TextAlign = System.Drawing.ContentAlignment.BottomCenter
    Me.ToolTip1.SetToolTip(Me.cmd_about, "Help Information")
    '
    'cmd_delete
    '
    Me.cmd_delete.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(192, Byte))
    Me.cmd_delete.Cursor = System.Windows.Forms.Cursors.Default
    Me.cmd_delete.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.cmd_delete.ForeColor = System.Drawing.SystemColors.ControlText
    Me.cmd_delete.Image = CType(resources.GetObject("cmd_delete.Image"), System.Drawing.Image)
    Me.cmd_delete.Location = New System.Drawing.Point(144, 0)
    Me.cmd_delete.Name = "cmd_delete"
    Me.cmd_delete.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.cmd_delete.Size = New System.Drawing.Size(25, 25)
    Me.cmd_delete.TabIndex = 20
    Me.cmd_delete.TextAlign = System.Drawing.ContentAlignment.BottomCenter
    Me.ToolTip1.SetToolTip(Me.cmd_delete, "Delete this record")
    '
    'cmd_add
    '
    Me.cmd_add.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(192, Byte))
    Me.cmd_add.Cursor = System.Windows.Forms.Cursors.Default
    Me.cmd_add.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.cmd_add.ForeColor = System.Drawing.SystemColors.ControlText
    Me.cmd_add.Image = CType(resources.GetObject("cmd_add.Image"), System.Drawing.Image)
    Me.cmd_add.Location = New System.Drawing.Point(120, 0)
    Me.cmd_add.Name = "cmd_add"
    Me.cmd_add.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.cmd_add.Size = New System.Drawing.Size(25, 25)
    Me.cmd_add.TabIndex = 19
    Me.cmd_add.TextAlign = System.Drawing.ContentAlignment.BottomCenter
    Me.ToolTip1.SetToolTip(Me.cmd_add, "Add a new record after this one")
    '
    'cmd_save
    '
    Me.cmd_save.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(192, Byte))
    Me.cmd_save.Cursor = System.Windows.Forms.Cursors.Default
    Me.cmd_save.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.cmd_save.ForeColor = System.Drawing.SystemColors.ControlText
    Me.cmd_save.Image = CType(resources.GetObject("cmd_save.Image"), System.Drawing.Image)
    Me.cmd_save.Location = New System.Drawing.Point(0, 0)
    Me.cmd_save.Name = "cmd_save"
    Me.cmd_save.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.cmd_save.Size = New System.Drawing.Size(25, 25)
    Me.cmd_save.TabIndex = 16
    Me.cmd_save.TextAlign = System.Drawing.ContentAlignment.BottomCenter
    Me.ToolTip1.SetToolTip(Me.cmd_save, "Save Changes to stations.js")
    '
    'cmd_undo
    '
    Me.cmd_undo.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(192, Byte))
    Me.cmd_undo.Cursor = System.Windows.Forms.Cursors.Default
    Me.cmd_undo.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.cmd_undo.ForeColor = System.Drawing.SystemColors.ControlText
    Me.cmd_undo.Image = CType(resources.GetObject("cmd_undo.Image"), System.Drawing.Image)
    Me.cmd_undo.Location = New System.Drawing.Point(88, 0)
    Me.cmd_undo.Name = "cmd_undo"
    Me.cmd_undo.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.cmd_undo.Size = New System.Drawing.Size(25, 25)
    Me.cmd_undo.TabIndex = 18
    Me.cmd_undo.TextAlign = System.Drawing.ContentAlignment.BottomCenter
    Me.ToolTip1.SetToolTip(Me.cmd_undo, "Undo changes to record")
    '
    'cmd_apply
    '
    Me.cmd_apply.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(192, Byte))
    Me.cmd_apply.Cursor = System.Windows.Forms.Cursors.Default
    Me.cmd_apply.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.cmd_apply.ForeColor = System.Drawing.SystemColors.ControlText
    Me.cmd_apply.Image = CType(resources.GetObject("cmd_apply.Image"), System.Drawing.Image)
    Me.cmd_apply.Location = New System.Drawing.Point(64, 0)
    Me.cmd_apply.Name = "cmd_apply"
    Me.cmd_apply.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.cmd_apply.Size = New System.Drawing.Size(25, 25)
    Me.cmd_apply.TabIndex = 17
    Me.cmd_apply.TextAlign = System.Drawing.ContentAlignment.BottomCenter
    Me.ToolTip1.SetToolTip(Me.cmd_apply, "Accept changes to record")
    '
    'scroll_station
    '
    Me.scroll_station.Cursor = System.Windows.Forms.Cursors.Default
    Me.scroll_station.Location = New System.Drawing.Point(48, 168)
    Me.scroll_station.Maximum = 32776
    Me.scroll_station.Name = "scroll_station"
    Me.scroll_station.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.scroll_station.Size = New System.Drawing.Size(241, 17)
    Me.scroll_station.TabIndex = 15
    Me.scroll_station.TabStop = True
    '
    'chk_edit
    '
    Me.chk_edit.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(128, Byte))
    Me.chk_edit.Cursor = System.Windows.Forms.Cursors.Default
    Me.chk_edit.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.chk_edit.ForeColor = System.Drawing.SystemColors.ControlText
    Me.chk_edit.Location = New System.Drawing.Point(32, 168)
    Me.chk_edit.Name = "chk_edit"
    Me.chk_edit.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.chk_edit.Size = New System.Drawing.Size(17, 17)
    Me.chk_edit.TabIndex = 14
    '
    'fra_station_edit
    '
    Me.fra_station_edit.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(128, Byte))
    Me.fra_station_edit.Controls.Add(Me.edit_daid)
    Me.fra_station_edit.Controls.Add(Me.edit_notes)
    Me.fra_station_edit.Controls.Add(Me.edit_lon)
    Me.fra_station_edit.Controls.Add(Me.edit_lat)
    Me.fra_station_edit.Controls.Add(Me.edit_sta)
    Me.fra_station_edit.Controls.Add(Me.edit_cnt)
    Me.fra_station_edit.Controls.Add(Me.edit_qth)
    Me.fra_station_edit.Controls.Add(Me.edit_pwr)
    Me.fra_station_edit.Controls.Add(Me.edit_khz)
    Me.fra_station_edit.Controls.Add(Me.edit_call)
    Me.fra_station_edit.Controls.Add(Me.edit_cyc)
    Me.fra_station_edit.Controls.Add(Me.edit_lsb)
    Me.fra_station_edit.Controls.Add(Me.edit_usb)
    Me.fra_station_edit.Controls.Add(Me.Label22)
    Me.fra_station_edit.Controls.Add(Me.Label21)
    Me.fra_station_edit.Controls.Add(Me.Label20)
    Me.fra_station_edit.Controls.Add(Me.Label19)
    Me.fra_station_edit.Controls.Add(Me.Label18)
    Me.fra_station_edit.Controls.Add(Me.Label17)
    Me.fra_station_edit.Controls.Add(Me.Label16)
    Me.fra_station_edit.Controls.Add(Me.Label15)
    Me.fra_station_edit.Controls.Add(Me.Label14)
    Me.fra_station_edit.Controls.Add(Me.Label13)
    Me.fra_station_edit.Controls.Add(Me.Label12)
    Me.fra_station_edit.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.fra_station_edit.ForeColor = System.Drawing.SystemColors.ControlText
    Me.fra_station_edit.Location = New System.Drawing.Point(0, 32)
    Me.fra_station_edit.Name = "fra_station_edit"
    Me.fra_station_edit.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.fra_station_edit.Size = New System.Drawing.Size(305, 129)
    Me.fra_station_edit.TabIndex = 0
    Me.fra_station_edit.TabStop = False
    Me.fra_station_edit.Text = "Station n of m"
    '
    'edit_daid
    '
    Me.edit_daid.BackColor = System.Drawing.SystemColors.Window
    Me.edit_daid.Cursor = System.Windows.Forms.Cursors.Default
    Me.edit_daid.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_daid.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_daid.ItemHeight = 14
    Me.edit_daid.Items.AddRange(New Object() {"?", "Y", "N"})
    Me.edit_daid.Location = New System.Drawing.Point(88, 32)
    Me.edit_daid.Name = "edit_daid"
    Me.edit_daid.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_daid.Size = New System.Drawing.Size(33, 18)
    Me.edit_daid.TabIndex = 3
    '
    'edit_notes
    '
    Me.edit_notes.AcceptsReturn = True
    Me.edit_notes.AutoSize = False
    Me.edit_notes.BackColor = System.Drawing.SystemColors.Window
    Me.edit_notes.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_notes.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_notes.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_notes.Location = New System.Drawing.Point(48, 104)
    Me.edit_notes.MaxLength = 0
    Me.edit_notes.Name = "edit_notes"
    Me.edit_notes.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_notes.Size = New System.Drawing.Size(233, 19)
    Me.edit_notes.TabIndex = 13
    Me.edit_notes.Text = ""
    '
    'edit_lon
    '
    Me.edit_lon.AcceptsReturn = True
    Me.edit_lon.AutoSize = False
    Me.edit_lon.BackColor = System.Drawing.SystemColors.Window
    Me.edit_lon.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_lon.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_lon.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_lon.Location = New System.Drawing.Point(184, 80)
    Me.edit_lon.MaxLength = 0
    Me.edit_lon.Name = "edit_lon"
    Me.edit_lon.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_lon.Size = New System.Drawing.Size(97, 19)
    Me.edit_lon.TabIndex = 12
    Me.edit_lon.Text = ""
    '
    'edit_lat
    '
    Me.edit_lat.AcceptsReturn = True
    Me.edit_lat.AutoSize = False
    Me.edit_lat.BackColor = System.Drawing.SystemColors.Window
    Me.edit_lat.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_lat.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_lat.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_lat.Location = New System.Drawing.Point(48, 80)
    Me.edit_lat.MaxLength = 0
    Me.edit_lat.Name = "edit_lat"
    Me.edit_lat.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_lat.Size = New System.Drawing.Size(97, 19)
    Me.edit_lat.TabIndex = 11
    Me.edit_lat.Text = ""
    '
    'edit_sta
    '
    Me.edit_sta.AcceptsReturn = True
    Me.edit_sta.AutoSize = False
    Me.edit_sta.BackColor = System.Drawing.SystemColors.Window
    Me.edit_sta.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_sta.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_sta.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_sta.Location = New System.Drawing.Point(208, 56)
    Me.edit_sta.MaxLength = 0
    Me.edit_sta.Name = "edit_sta"
    Me.edit_sta.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_sta.Size = New System.Drawing.Size(33, 19)
    Me.edit_sta.TabIndex = 9
    Me.edit_sta.Text = ""
    '
    'edit_cnt
    '
    Me.edit_cnt.AcceptsReturn = True
    Me.edit_cnt.AutoSize = False
    Me.edit_cnt.BackColor = System.Drawing.SystemColors.Window
    Me.edit_cnt.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_cnt.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_cnt.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_cnt.Location = New System.Drawing.Point(248, 56)
    Me.edit_cnt.MaxLength = 0
    Me.edit_cnt.Name = "edit_cnt"
    Me.edit_cnt.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_cnt.Size = New System.Drawing.Size(33, 19)
    Me.edit_cnt.TabIndex = 10
    Me.edit_cnt.Text = ""
    '
    'edit_qth
    '
    Me.edit_qth.AcceptsReturn = True
    Me.edit_qth.AutoSize = False
    Me.edit_qth.BackColor = System.Drawing.SystemColors.Window
    Me.edit_qth.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_qth.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_qth.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_qth.Location = New System.Drawing.Point(48, 56)
    Me.edit_qth.MaxLength = 0
    Me.edit_qth.Name = "edit_qth"
    Me.edit_qth.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_qth.Size = New System.Drawing.Size(153, 19)
    Me.edit_qth.TabIndex = 8
    Me.edit_qth.Text = ""
    '
    'edit_pwr
    '
    Me.edit_pwr.AcceptsReturn = True
    Me.edit_pwr.AutoSize = False
    Me.edit_pwr.BackColor = System.Drawing.SystemColors.Window
    Me.edit_pwr.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_pwr.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_pwr.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_pwr.Location = New System.Drawing.Point(248, 32)
    Me.edit_pwr.MaxLength = 0
    Me.edit_pwr.Name = "edit_pwr"
    Me.edit_pwr.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_pwr.Size = New System.Drawing.Size(33, 19)
    Me.edit_pwr.TabIndex = 7
    Me.edit_pwr.Text = ""
    '
    'edit_khz
    '
    Me.edit_khz.AcceptsReturn = True
    Me.edit_khz.AutoSize = False
    Me.edit_khz.BackColor = System.Drawing.SystemColors.Window
    Me.edit_khz.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_khz.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_khz.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_khz.Location = New System.Drawing.Point(8, 32)
    Me.edit_khz.MaxLength = 0
    Me.edit_khz.Name = "edit_khz"
    Me.edit_khz.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_khz.Size = New System.Drawing.Size(33, 19)
    Me.edit_khz.TabIndex = 1
    Me.edit_khz.Text = ""
    '
    'edit_call
    '
    Me.edit_call.AcceptsReturn = True
    Me.edit_call.AutoSize = False
    Me.edit_call.BackColor = System.Drawing.SystemColors.Window
    Me.edit_call.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_call.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_call.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_call.Location = New System.Drawing.Point(48, 32)
    Me.edit_call.MaxLength = 0
    Me.edit_call.Name = "edit_call"
    Me.edit_call.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_call.Size = New System.Drawing.Size(33, 19)
    Me.edit_call.TabIndex = 2
    Me.edit_call.Text = ""
    '
    'edit_cyc
    '
    Me.edit_cyc.AcceptsReturn = True
    Me.edit_cyc.AutoSize = False
    Me.edit_cyc.BackColor = System.Drawing.SystemColors.Window
    Me.edit_cyc.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_cyc.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_cyc.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_cyc.Location = New System.Drawing.Point(128, 32)
    Me.edit_cyc.MaxLength = 0
    Me.edit_cyc.Name = "edit_cyc"
    Me.edit_cyc.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_cyc.Size = New System.Drawing.Size(33, 19)
    Me.edit_cyc.TabIndex = 4
    Me.edit_cyc.Text = ""
    '
    'edit_lsb
    '
    Me.edit_lsb.AcceptsReturn = True
    Me.edit_lsb.AutoSize = False
    Me.edit_lsb.BackColor = System.Drawing.SystemColors.Window
    Me.edit_lsb.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_lsb.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_lsb.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_lsb.Location = New System.Drawing.Point(168, 32)
    Me.edit_lsb.MaxLength = 0
    Me.edit_lsb.Name = "edit_lsb"
    Me.edit_lsb.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_lsb.Size = New System.Drawing.Size(33, 19)
    Me.edit_lsb.TabIndex = 5
    Me.edit_lsb.Text = ""
    '
    'edit_usb
    '
    Me.edit_usb.AcceptsReturn = True
    Me.edit_usb.AutoSize = False
    Me.edit_usb.BackColor = System.Drawing.SystemColors.Window
    Me.edit_usb.Cursor = System.Windows.Forms.Cursors.IBeam
    Me.edit_usb.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.edit_usb.ForeColor = System.Drawing.SystemColors.WindowText
    Me.edit_usb.Location = New System.Drawing.Point(208, 32)
    Me.edit_usb.MaxLength = 0
    Me.edit_usb.Name = "edit_usb"
    Me.edit_usb.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.edit_usb.Size = New System.Drawing.Size(33, 19)
    Me.edit_usb.TabIndex = 6
    Me.edit_usb.Text = ""
    '
    'Label22
    '
    Me.Label22.BackColor = System.Drawing.Color.Transparent
    Me.Label22.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label22.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label22.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label22.Location = New System.Drawing.Point(0, 107)
    Me.Label22.Name = "Label22"
    Me.Label22.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label22.Size = New System.Drawing.Size(40, 17)
    Me.Label22.TabIndex = 56
    Me.Label22.Text = "Notes"
    Me.Label22.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'Label21
    '
    Me.Label21.BackColor = System.Drawing.Color.Transparent
    Me.Label21.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label21.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label21.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label21.Location = New System.Drawing.Point(208, 16)
    Me.Label21.Name = "Label21"
    Me.Label21.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label21.Size = New System.Drawing.Size(28, 17)
    Me.Label21.TabIndex = 44
    Me.Label21.Text = "USB"
    '
    'Label20
    '
    Me.Label20.BackColor = System.Drawing.Color.Transparent
    Me.Label20.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label20.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label20.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label20.Location = New System.Drawing.Point(168, 16)
    Me.Label20.Name = "Label20"
    Me.Label20.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label20.Size = New System.Drawing.Size(28, 17)
    Me.Label20.TabIndex = 43
    Me.Label20.Text = "LSB"
    '
    'Label19
    '
    Me.Label19.BackColor = System.Drawing.Color.Transparent
    Me.Label19.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label19.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label19.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label19.Location = New System.Drawing.Point(128, 16)
    Me.Label19.Name = "Label19"
    Me.Label19.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label19.Size = New System.Drawing.Size(36, 17)
    Me.Label19.TabIndex = 42
    Me.Label19.Text = "Cycle"
    '
    'Label18
    '
    Me.Label18.BackColor = System.Drawing.Color.Transparent
    Me.Label18.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label18.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label18.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label18.Location = New System.Drawing.Point(88, 16)
    Me.Label18.Name = "Label18"
    Me.Label18.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label18.Size = New System.Drawing.Size(32, 17)
    Me.Label18.TabIndex = 41
    Me.Label18.Text = "DAID"
    '
    'Label17
    '
    Me.Label17.BackColor = System.Drawing.Color.Transparent
    Me.Label17.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label17.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label17.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label17.Location = New System.Drawing.Point(48, 16)
    Me.Label17.Name = "Label17"
    Me.Label17.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label17.Size = New System.Drawing.Size(28, 17)
    Me.Label17.TabIndex = 40
    Me.Label17.Text = "Call"
    '
    'Label16
    '
    Me.Label16.BackColor = System.Drawing.Color.Transparent
    Me.Label16.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label16.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label16.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label16.Location = New System.Drawing.Point(16, 16)
    Me.Label16.Name = "Label16"
    Me.Label16.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label16.Size = New System.Drawing.Size(25, 17)
    Me.Label16.TabIndex = 39
    Me.Label16.Text = "KHz"
    '
    'Label15
    '
    Me.Label15.BackColor = System.Drawing.Color.Transparent
    Me.Label15.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label15.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label15.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label15.Location = New System.Drawing.Point(248, 16)
    Me.Label15.Name = "Label15"
    Me.Label15.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label15.Size = New System.Drawing.Size(44, 17)
    Me.Label15.TabIndex = 38
    Me.Label15.Text = "Pwr(W)"
    '
    'Label14
    '
    Me.Label14.BackColor = System.Drawing.Color.Transparent
    Me.Label14.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label14.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label14.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label14.Location = New System.Drawing.Point(8, 59)
    Me.Label14.Name = "Label14"
    Me.Label14.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label14.Size = New System.Drawing.Size(32, 17)
    Me.Label14.TabIndex = 37
    Me.Label14.Text = "QTH"
    Me.Label14.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'Label13
    '
    Me.Label13.BackColor = System.Drawing.Color.Transparent
    Me.Label13.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label13.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label13.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label13.Location = New System.Drawing.Point(16, 83)
    Me.Label13.Name = "Label13"
    Me.Label13.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label13.Size = New System.Drawing.Size(24, 17)
    Me.Label13.TabIndex = 36
    Me.Label13.Text = "Lat"
    Me.Label13.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'Label12
    '
    Me.Label12.BackColor = System.Drawing.Color.Transparent
    Me.Label12.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label12.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Bold)
    Me.Label12.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label12.Location = New System.Drawing.Point(152, 83)
    Me.Label12.Name = "Label12"
    Me.Label12.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label12.Size = New System.Drawing.Size(25, 17)
    Me.Label12.TabIndex = 35
    Me.Label12.Text = "Lon"
    Me.Label12.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'fra_station_show
    '
    Me.fra_station_show.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(128, Byte))
    Me.fra_station_show.Controls.Add(Me.show_notes)
    Me.fra_station_show.Controls.Add(Me.Label23)
    Me.fra_station_show.Controls.Add(Me.show_lon)
    Me.fra_station_show.Controls.Add(Me.show_lat)
    Me.fra_station_show.Controls.Add(Me.show_cnt)
    Me.fra_station_show.Controls.Add(Me.show_sta)
    Me.fra_station_show.Controls.Add(Me.show_qth)
    Me.fra_station_show.Controls.Add(Me.show_pwr)
    Me.fra_station_show.Controls.Add(Me.show_usb)
    Me.fra_station_show.Controls.Add(Me.show_lsb)
    Me.fra_station_show.Controls.Add(Me.show_cyc)
    Me.fra_station_show.Controls.Add(Me.show_daid)
    Me.fra_station_show.Controls.Add(Me.show_call)
    Me.fra_station_show.Controls.Add(Me.Label11)
    Me.fra_station_show.Controls.Add(Me.Label6)
    Me.fra_station_show.Controls.Add(Me.Label5)
    Me.fra_station_show.Controls.Add(Me.Label4)
    Me.fra_station_show.Controls.Add(Me.Label3)
    Me.fra_station_show.Controls.Add(Me.label2)
    Me.fra_station_show.Controls.Add(Me.Label7)
    Me.fra_station_show.Controls.Add(Me.Label8)
    Me.fra_station_show.Controls.Add(Me.Label9)
    Me.fra_station_show.Controls.Add(Me.Label10)
    Me.fra_station_show.Controls.Add(Me.show_khz)
    Me.fra_station_show.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.fra_station_show.ForeColor = System.Drawing.SystemColors.ControlText
    Me.fra_station_show.Location = New System.Drawing.Point(0, 32)
    Me.fra_station_show.Name = "fra_station_show"
    Me.fra_station_show.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.fra_station_show.Size = New System.Drawing.Size(305, 129)
    Me.fra_station_show.TabIndex = 23
    Me.fra_station_show.TabStop = False
    Me.fra_station_show.Text = "Station n of m"
    Me.fra_station_show.Visible = False
    '
    'show_notes
    '
    Me.show_notes.BackColor = System.Drawing.Color.White
    Me.show_notes.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_notes.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_notes.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_notes.Location = New System.Drawing.Point(48, 107)
    Me.show_notes.Name = "show_notes"
    Me.show_notes.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_notes.Size = New System.Drawing.Size(232, 17)
    Me.show_notes.TabIndex = 58
    '
    'Label23
    '
    Me.Label23.BackColor = System.Drawing.Color.Transparent
    Me.Label23.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label23.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label23.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label23.Location = New System.Drawing.Point(0, 107)
    Me.Label23.Name = "Label23"
    Me.Label23.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label23.Size = New System.Drawing.Size(40, 17)
    Me.Label23.TabIndex = 57
    Me.Label23.Text = "Notes"
    Me.Label23.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'show_lon
    '
    Me.show_lon.BackColor = System.Drawing.Color.White
    Me.show_lon.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_lon.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_lon.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_lon.Location = New System.Drawing.Point(187, 83)
    Me.show_lon.Name = "show_lon"
    Me.show_lon.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_lon.Size = New System.Drawing.Size(93, 17)
    Me.show_lon.TabIndex = 55
    '
    'show_lat
    '
    Me.show_lat.BackColor = System.Drawing.Color.White
    Me.show_lat.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_lat.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_lat.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_lat.Location = New System.Drawing.Point(48, 83)
    Me.show_lat.Name = "show_lat"
    Me.show_lat.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_lat.Size = New System.Drawing.Size(88, 17)
    Me.show_lat.TabIndex = 54
    '
    'show_cnt
    '
    Me.show_cnt.BackColor = System.Drawing.Color.White
    Me.show_cnt.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_cnt.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_cnt.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_cnt.Location = New System.Drawing.Point(248, 59)
    Me.show_cnt.Name = "show_cnt"
    Me.show_cnt.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_cnt.Size = New System.Drawing.Size(32, 17)
    Me.show_cnt.TabIndex = 53
    '
    'show_sta
    '
    Me.show_sta.BackColor = System.Drawing.Color.White
    Me.show_sta.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_sta.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_sta.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_sta.Location = New System.Drawing.Point(208, 59)
    Me.show_sta.Name = "show_sta"
    Me.show_sta.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_sta.Size = New System.Drawing.Size(32, 17)
    Me.show_sta.TabIndex = 52
    '
    'show_qth
    '
    Me.show_qth.BackColor = System.Drawing.Color.White
    Me.show_qth.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_qth.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_qth.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_qth.Location = New System.Drawing.Point(48, 59)
    Me.show_qth.Name = "show_qth"
    Me.show_qth.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_qth.Size = New System.Drawing.Size(144, 17)
    Me.show_qth.TabIndex = 51
    '
    'show_pwr
    '
    Me.show_pwr.BackColor = System.Drawing.Color.White
    Me.show_pwr.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_pwr.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_pwr.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_pwr.Location = New System.Drawing.Point(248, 35)
    Me.show_pwr.Name = "show_pwr"
    Me.show_pwr.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_pwr.Size = New System.Drawing.Size(32, 17)
    Me.show_pwr.TabIndex = 50
    '
    'show_usb
    '
    Me.show_usb.BackColor = System.Drawing.Color.White
    Me.show_usb.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_usb.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_usb.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_usb.Location = New System.Drawing.Point(208, 35)
    Me.show_usb.Name = "show_usb"
    Me.show_usb.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_usb.Size = New System.Drawing.Size(32, 17)
    Me.show_usb.TabIndex = 49
    '
    'show_lsb
    '
    Me.show_lsb.BackColor = System.Drawing.Color.White
    Me.show_lsb.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_lsb.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_lsb.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_lsb.Location = New System.Drawing.Point(168, 35)
    Me.show_lsb.Name = "show_lsb"
    Me.show_lsb.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_lsb.Size = New System.Drawing.Size(32, 17)
    Me.show_lsb.TabIndex = 48
    '
    'show_cyc
    '
    Me.show_cyc.BackColor = System.Drawing.Color.White
    Me.show_cyc.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_cyc.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_cyc.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_cyc.Location = New System.Drawing.Point(128, 35)
    Me.show_cyc.Name = "show_cyc"
    Me.show_cyc.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_cyc.Size = New System.Drawing.Size(32, 17)
    Me.show_cyc.TabIndex = 47
    '
    'show_daid
    '
    Me.show_daid.BackColor = System.Drawing.Color.White
    Me.show_daid.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_daid.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_daid.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_daid.Location = New System.Drawing.Point(91, 35)
    Me.show_daid.Name = "show_daid"
    Me.show_daid.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_daid.Size = New System.Drawing.Size(9, 17)
    Me.show_daid.TabIndex = 46
    Me.show_daid.Text = "?"
    '
    'show_call
    '
    Me.show_call.BackColor = System.Drawing.Color.White
    Me.show_call.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_call.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_call.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_call.Location = New System.Drawing.Point(48, 35)
    Me.show_call.Name = "show_call"
    Me.show_call.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_call.Size = New System.Drawing.Size(32, 17)
    Me.show_call.TabIndex = 45
    '
    'Label11
    '
    Me.Label11.BackColor = System.Drawing.Color.Transparent
    Me.Label11.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label11.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label11.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label11.Location = New System.Drawing.Point(208, 16)
    Me.Label11.Name = "Label11"
    Me.Label11.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label11.Size = New System.Drawing.Size(28, 17)
    Me.Label11.TabIndex = 34
    Me.Label11.Text = "USB"
    '
    'Label6
    '
    Me.Label6.BackColor = System.Drawing.Color.Transparent
    Me.Label6.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label6.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label6.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label6.Location = New System.Drawing.Point(168, 16)
    Me.Label6.Name = "Label6"
    Me.Label6.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label6.Size = New System.Drawing.Size(28, 17)
    Me.Label6.TabIndex = 33
    Me.Label6.Text = "LSB"
    '
    'Label5
    '
    Me.Label5.BackColor = System.Drawing.Color.Transparent
    Me.Label5.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label5.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label5.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label5.Location = New System.Drawing.Point(128, 16)
    Me.Label5.Name = "Label5"
    Me.Label5.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label5.Size = New System.Drawing.Size(36, 17)
    Me.Label5.TabIndex = 32
    Me.Label5.Text = "Cycle"
    '
    'Label4
    '
    Me.Label4.BackColor = System.Drawing.Color.Transparent
    Me.Label4.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label4.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label4.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label4.Location = New System.Drawing.Point(88, 16)
    Me.Label4.Name = "Label4"
    Me.Label4.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label4.Size = New System.Drawing.Size(32, 17)
    Me.Label4.TabIndex = 31
    Me.Label4.Text = "DAID"
    '
    'Label3
    '
    Me.Label3.BackColor = System.Drawing.Color.Transparent
    Me.Label3.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label3.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label3.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label3.Location = New System.Drawing.Point(48, 16)
    Me.Label3.Name = "Label3"
    Me.Label3.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label3.Size = New System.Drawing.Size(32, 17)
    Me.Label3.TabIndex = 30
    Me.Label3.Text = "Call"
    '
    'label2
    '
    Me.label2.BackColor = System.Drawing.Color.Transparent
    Me.label2.Cursor = System.Windows.Forms.Cursors.Default
    Me.label2.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.label2.ForeColor = System.Drawing.SystemColors.ControlText
    Me.label2.Location = New System.Drawing.Point(16, 16)
    Me.label2.Name = "label2"
    Me.label2.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.label2.Size = New System.Drawing.Size(32, 17)
    Me.label2.TabIndex = 29
    Me.label2.Text = "KHz"
    '
    'Label7
    '
    Me.Label7.BackColor = System.Drawing.Color.Transparent
    Me.Label7.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label7.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label7.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label7.Location = New System.Drawing.Point(248, 16)
    Me.Label7.Name = "Label7"
    Me.Label7.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label7.Size = New System.Drawing.Size(44, 17)
    Me.Label7.TabIndex = 28
    Me.Label7.Text = "Pwr(W)"
    '
    'Label8
    '
    Me.Label8.BackColor = System.Drawing.Color.Transparent
    Me.Label8.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label8.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label8.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label8.Location = New System.Drawing.Point(8, 59)
    Me.Label8.Name = "Label8"
    Me.Label8.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label8.Size = New System.Drawing.Size(32, 17)
    Me.Label8.TabIndex = 27
    Me.Label8.Text = "QTH"
    Me.Label8.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'Label9
    '
    Me.Label9.BackColor = System.Drawing.Color.Transparent
    Me.Label9.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label9.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label9.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label9.Location = New System.Drawing.Point(8, 83)
    Me.Label9.Name = "Label9"
    Me.Label9.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label9.Size = New System.Drawing.Size(32, 17)
    Me.Label9.TabIndex = 26
    Me.Label9.Text = "Lat"
    Me.Label9.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'Label10
    '
    Me.Label10.BackColor = System.Drawing.Color.Transparent
    Me.Label10.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label10.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label10.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label10.Location = New System.Drawing.Point(152, 83)
    Me.Label10.Name = "Label10"
    Me.Label10.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label10.Size = New System.Drawing.Size(25, 17)
    Me.Label10.TabIndex = 25
    Me.Label10.Text = "Lon"
    Me.Label10.TextAlign = System.Drawing.ContentAlignment.TopRight
    '
    'show_khz
    '
    Me.show_khz.BackColor = System.Drawing.Color.White
    Me.show_khz.Cursor = System.Windows.Forms.Cursors.Default
    Me.show_khz.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.show_khz.ForeColor = System.Drawing.SystemColors.ControlText
    Me.show_khz.Location = New System.Drawing.Point(11, 35)
    Me.show_khz.Name = "show_khz"
    Me.show_khz.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.show_khz.Size = New System.Drawing.Size(33, 17)
    Me.show_khz.TabIndex = 22
    '
    'Shape1
    '
    Me.Shape1.BackColor = System.Drawing.Color.Yellow
    Me.Shape1.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
    Me.Shape1.Location = New System.Drawing.Point(0, 0)
    Me.Shape1.Name = "Shape1"
    Me.Shape1.Size = New System.Drawing.Size(305, 25)
    Me.Shape1.TabIndex = 24
    '
    'Label1
    '
    Me.Label1.BackColor = System.Drawing.Color.Transparent
    Me.Label1.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label1.Font = New System.Drawing.Font("Arial", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label1.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Label1.Location = New System.Drawing.Point(8, 168)
    Me.Label1.Name = "Label1"
    Me.Label1.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label1.Size = New System.Drawing.Size(32, 17)
    Me.Label1.TabIndex = 24
    Me.Label1.Text = "Edit"
    '
    'frm_stations
    '
    Me.AutoScaleBaseSize = New System.Drawing.Size(5, 13)
    Me.BackColor = System.Drawing.Color.FromArgb(CType(255, Byte), CType(255, Byte), CType(128, Byte))
    Me.ClientSize = New System.Drawing.Size(304, 192)
    Me.Controls.Add(Me.cmd_about)
    Me.Controls.Add(Me.cmd_delete)
    Me.Controls.Add(Me.cmd_add)
    Me.Controls.Add(Me.cmd_save)
    Me.Controls.Add(Me.cmd_undo)
    Me.Controls.Add(Me.cmd_apply)
    Me.Controls.Add(Me.scroll_station)
    Me.Controls.Add(Me.chk_edit)
    Me.Controls.Add(Me.Shape1)
    Me.Controls.Add(Me.Label1)
    Me.Controls.Add(Me.fra_station_edit)
    Me.Controls.Add(Me.fra_station_show)
    Me.Cursor = System.Windows.Forms.Cursors.Default
    Me.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Icon = CType(resources.GetObject("$this.Icon"), System.Drawing.Icon)
    Me.Location = New System.Drawing.Point(4, 23)
    Me.Name = "frm_stations"
    Me.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Text = "NDB Weblog Station Editor"
    Me.fra_station_edit.ResumeLayout(False)
    Me.fra_station_show.ResumeLayout(False)
    Me.ResumeLayout(False)

  End Sub
#End Region 
#Region "Upgrade Support "
	Private Shared m_vb6FormDefInstance As frm_stations
	Private Shared m_InitializingDefInstance As Boolean
	Public Shared Property DefInstance() As frm_stations
		Get
			If m_vb6FormDefInstance Is Nothing OrElse m_vb6FormDefInstance.IsDisposed Then
				m_InitializingDefInstance = True
				m_vb6FormDefInstance = New frm_stations()
				m_InitializingDefInstance = False
			End If
			DefInstance = m_vb6FormDefInstance
		End Get
		Set
			m_vb6FormDefInstance = Value
		End Set
	End Property
#End Region 
	
	Dim backup_file, header, data_path, data_file As String
  Dim save_alert_shown As Boolean
	Dim stations() As STATION
	Dim stationsCount, stationCurrent As Short 'Array to hold all data
	Dim stationLoaded, editStatus, recordChanged, saveEnabled As Boolean
	Private Sub frm_stations_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles MyBase.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub cmd_save_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles cmd_save.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub cmd_apply_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles cmd_apply.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub cmd_undo_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles cmd_undo.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub cmd_add_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles cmd_add.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub cmd_delete_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles cmd_delete.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub cmd_about_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles cmd_about.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub scroll_station_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles scroll_station.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub chk_edit_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles chk_edit.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_khz_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_khz.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_Call_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_Call.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_daid_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_daid.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_cyc_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_cyc.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_lsb_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_lsb.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_usb_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_usb.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_pwr_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_pwr.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_qth_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_qth.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_sta_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_sta.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_cnt_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_cnt.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_lat_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_lat.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_lon_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_lon.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	Private Sub edit_notes_KeyDown(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.KeyEventArgs) Handles edit_notes.KeyDown
		Dim KeyCode As Short = eventArgs.KeyCode
		Dim Shift As Short = eventArgs.KeyData \ &H10000
		Call KeyDown_Renamed(KeyCode, Shift)
	End Sub
	'UPGRADE_NOTE: KeyDown was upgraded to KeyDown_Renamed. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1061"'
	Sub KeyDown_Renamed(ByRef KeyCode As Short, ByRef Shift As Short)
		If ((Shift And VB6.ShiftConstants.CtrlMask) > 0 And KeyCode = System.Windows.Forms.Keys.S) Then
			Call cmd_save_Click(cmd_save, New System.EventArgs())
		End If
	End Sub
	
	
	Sub frm_stations_Load(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles MyBase.Load
		Dim i As Short
		'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
		frm_stations.DefInstance.Text = get_name()
		frm_stations.DefInstance.Height = VB6.TwipsToPixelsY(3400)
		editStatus = False
		saveEnabled = False
		recordChanged = False
		stationLoaded = False
		stationCurrent = 0
		backup_file = "stations.bak"
		data_file = "stations.js"
		If (data_path = "") Then
			If (VB.Command() <> "") Then
				data_path = VB.Command()
			Else
				data_path = VB6.GetPath
			End If
		End If
		'UPGRADE_WARNING: Couldn't resolve default property of object save_alert_shown. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
		save_alert_shown = 0
		Call load_stations()
		Call update_station()
		Call update_status()
		scroll_station.Maximum = (stationsCount + scroll_station.LargeChange - 1)
		stationLoaded = True
	End Sub
	
	Private Sub cmd_about_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmd_about.Click
		frm_about.DefInstance.Visible = True
	End Sub
	
	Sub get_path()
		Dim bi As BROWSEINFO
		Dim pidl As Integer
		Dim path As String
		Dim pos As Short
		bi.hOwner = Me.Handle.ToInt32
		bi.pidlRoot = 0
		bi.lpszTitle = "Select the directory where your " & data_file & " file resides"
		bi.ulFlags = BIF_RETURNONLYFSDIRS
		pidl = SHBrowseForFolder(bi)
		path = Space(MAX_PATH)
		If SHGetPathFromIDList(pidl, path) Then
			pos = InStr(path, Chr(0))
			data_path = VB.Left(path, pos - 1)
		End If
		Call CoTaskMemFree(pidl)
	End Sub
	
	Sub add_station()
		Dim i As Short
		Dim new_station As STATION
		new_station.daid = "Y"
		stationsCount = stationsCount + 1
		ReDim Preserve stations(stationsCount)
		For i = stationsCount - 1 To stationCurrent Step -1
			'UPGRADE_WARNING: Couldn't resolve default property of object stations(i + 1). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
			stations(i + 1) = stations(i)
		Next 
		
		stationCurrent = stationCurrent + 1
		
		'UPGRADE_WARNING: Couldn't resolve default property of object stations(stationCurrent). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
		stations(stationCurrent) = new_station
		stations(stationCurrent).daid = "?"
		scroll_station.Maximum = (stationsCount + scroll_station.LargeChange - 1)
		scroll_station.Value = stationCurrent
		saveEnabled = True
		Call scroll_station_Change(0)
		Call update_status()
		Call update_station()
	End Sub
	Sub delete_station()
		Dim i As Short
		Dim temp_array() As STATION
		Dim new_index As Short
		If (stationsCount > 0) Then
			saveEnabled = True
			
			ReDim temp_array(stationsCount)
			new_index = 0
			
			For i = 0 To stationsCount
				If (i <> stationCurrent) Then
					'UPGRADE_WARNING: Couldn't resolve default property of object temp_array(new_index). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
					temp_array(new_index) = stations(i)
					new_index = new_index + 1
				End If
			Next 
			stationsCount = stationsCount - 1
			ReDim stations(stationsCount)
			For i = 0 To stationsCount
				'UPGRADE_WARNING: Couldn't resolve default property of object stations(i). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
				stations(i) = temp_array(i)
			Next 
			If (stationCurrent > stationsCount) Then
				stationCurrent = stationsCount
			End If
			scroll_station.Maximum = (stationsCount + scroll_station.LargeChange - 1)
			scroll_station.Value = stationCurrent
			Call scroll_station_Change(0)
			Call update_status()
			Call update_station()
		End If
	End Sub
	'UPGRADE_WARNING: Event chk_edit.CheckStateChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub chk_edit_CheckStateChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles chk_edit.CheckStateChanged
		editStatus = Not editStatus
		Call update_station()
		Call update_status()
	End Sub
	Private Sub cmd_add_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmd_add.Click
		If (recordChanged) Then
			Call cmd_apply_Click(cmd_apply, New System.EventArgs())
		End If
		Call add_station()
	End Sub
	Private Sub cmd_delete_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmd_delete.Click
		Call delete_station()
	End Sub
	Private Sub cmd_save_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmd_save.Click
		Call update_coords()
		Call update_record()
		Call edit()
		Call save_stations()
		Call update_status()
	End Sub
	Private Sub cmd_undo_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmd_undo.Click
		Call update_station()
	End Sub
	'UPGRADE_WARNING: Event edit_khz.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_khz_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_khz.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_call.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_call_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_call.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_daid.SelectedIndexChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_daid_SelectedIndexChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_daid.SelectedIndexChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_cyc.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_cyc_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_cyc.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_lsb.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_lsb_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_lsb.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_usb.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_usb_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_usb.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_pwr.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_pwr_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_pwr.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_qth.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_qth_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_qth.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_sta.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_sta_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_sta.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_cnt.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_cnt_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_cnt.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_lat.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_lat_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_lat.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_lon.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_lon_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_lon.TextChanged
		Call edit()
	End Sub
	'UPGRADE_WARNING: Event edit_notes.TextChanged may fire when form is initialized. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2075"'
	Private Sub edit_notes_TextChanged(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles edit_notes.TextChanged
		Call edit()
	End Sub
	Private Sub cmd_apply_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmd_apply.Click
		Call update_coords()
		Call update_record()
		Call update_status()
		Call edit()
	End Sub
	Sub update_record()
		Dim temp, i As Object
		edit_call.Text = UCase(edit_call.Text)
		edit_sta.Text = UCase(edit_sta.Text)
		edit_cnt.Text = UCase(edit_cnt.Text)
		
		If (edit_qth.Text <> "") Then
			'UPGRADE_WARNING: Couldn't resolve default property of object temp. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
			temp = Trim(LCase(edit_qth.Text)) ' Convert the title only to lower case
			'UPGRADE_WARNING: Couldn't resolve default property of object temp. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
			Mid(temp, 1, 1) = UCase(Mid(temp, 1, 1))
			For i = 2 To Len(temp) Step 1
				'UPGRADE_WARNING: Couldn't resolve default property of object i. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
				'UPGRADE_WARNING: Couldn't resolve default property of object temp. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
				If Mid(temp, i, 1) = " " Then
					'UPGRADE_WARNING: Couldn't resolve default property of object i. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
					'UPGRADE_WARNING: Couldn't resolve default property of object temp. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
					Mid(temp, i + 1, 1) = UCase(Mid(temp, i + 1, 1))
				End If
			Next i
			'UPGRADE_WARNING: Couldn't resolve default property of object temp. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
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
			frm_stations.DefInstance.Height = VB6.TwipsToPixelsY(3400)
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
		Dim i As Short
		Dim sep As String
		Dim a As STATION
		On Error Resume Next
		
		sep = Chr(34) & "," & Chr(34)
		
		Kill(data_path & "\" & backup_file)
		If (Err.Number <> 0 And Err.Number <> 53) Then 'This isn't file not found
			MsgBox(Err.Description)
		End If
		
		If (Err.Number <> 53) Then
			'UPGRADE_WARNING: Couldn't resolve default property of object save_alert_shown. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
      If (Not save_alert_shown) Then
        'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
        MsgBox("Old " & data_file & " file has been renamed to " & backup_file & "." & Chr(13) & "From now on, you won't see this message.", MsgBoxStyle.OKOnly + MsgBoxStyle.Information, get_name())
        'UPGRADE_WARNING: Couldn't resolve default property of object save_alert_shown. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
        save_alert_shown = True
      End If
    End If
		
		Rename(data_path & "\" & data_file, data_path & "\" & backup_file)
		
		
		FileOpen(1, data_path & "\" & data_file, OpenMode.Output) ' Open file.
		PrintLine(1, header)
		For i = 0 To stationsCount
			'UPGRADE_WARNING: Couldn't resolve default property of object a. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
			a = stations(i)
			PrintLine(1, "STATION (" & Chr(34) & a.khz & sep & a.cal & sep & a.qth & sep & a.sta & sep & a.cnt & sep & a.cyc & sep & a.daid & sep & a.lsb & sep & a.usb & sep & a.pwr & sep & a.lat & sep & a.lon & sep & a.notes & Chr(34) & ");")
		Next 
		FileClose(1)
		saveEnabled = False
	End Sub
	
	Sub load_stations()
		Dim temp As String
		Dim tempStation As STATION
		Dim startPos, endPos As Short
		Dim FileNumber, stationsSize, response As Short
		stationsSize = 50
		header = ""
		On Error Resume Next
		FileNumber = FreeFile
		
		FileOpen(FileNumber, data_path & "\" & data_file, OpenMode.Input) ' Open file.
		
		If Err.Number = 53 Then
			'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
			MsgBox("The " & data_file & " file isn't in the" & Chr(13) & data_path & " folder.", MsgBoxStyle.Exclamation + MsgBoxStyle.OKOnly, get_name())
		End If
		
		Do While (Err.Number = 53)
			Err.Clear()
			'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
			response = MsgBox("Please specify where the " & data_file & " file you wish to edit is located," & Chr(13) & "or press cancel to quit", MsgBoxStyle.Exclamation + MsgBoxStyle.OKCancel, get_name())
			If (response = MsgBoxResult.Cancel) Then
				'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
				MsgBox("Quitting NDB Weblog Station Editor", MsgBoxStyle.Information + MsgBoxStyle.OKOnly, get_name())
				End
			End If
			Call get_path()
			FileOpen(FileNumber, data_path & "\" & data_file, OpenMode.Input) ' Open file.
		Loop 
		
		
		
		ReDim stations(stationsSize)
		Do While Not EOF(FileNumber)
			temp = LineInput(FileNumber) ' Get each line contents.
			If (VB.Left(temp, 7) = "STATION") Then
				' Ensure that stations() is big enough to handle all stations
				If (stationsCount > stationsSize) Then
					stationsSize = stationsSize + 50
					ReDim Preserve stations(stationsSize)
				End If
				Err()
				startPos = InStr(1, temp, Chr(34)) + 1
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.khz = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.cal = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.qth = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.sta = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.cnt = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.cyc = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.daid = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.lsb = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.usb = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.pwr = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.lat = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.lon = Mid(temp, startPos, endPos - startPos)
				
				startPos = endPos + 3
				endPos = InStr(startPos, temp, Chr(34))
				tempStation.notes = Mid(temp, startPos, endPos - startPos)
				
				'UPGRADE_WARNING: Couldn't resolve default property of object stations(stationsCount). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
				stations(stationsCount) = tempStation
				stationsCount = stationsCount + 1
				
				'Debug.Print temp  ' Print to Debug window.
			Else
				header = header & temp & Chr(13)
			End If
		Loop 
		stationsCount = stationsCount - 1
		header = VB.Left(header, Len(header) - 1)
		FileClose(FileNumber) ' Close file.
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
		fra_station_edit.Text = "Station " & (stationCurrent + 1) & " of " & (stationsCount + 1) & " (Edit Mode)"
		show_khz.Text = stations(stationCurrent).khz
		show_call.Text = stations(stationCurrent).cal
		show_daid.Text = stations(stationCurrent).daid
		show_cyc.Text = stations(stationCurrent).cyc
		show_lsb.Text = stations(stationCurrent).lsb
		show_usb.Text = stations(stationCurrent).usb
		show_pwr.Text = stations(stationCurrent).pwr
		show_qth.Text = stations(stationCurrent).qth
		show_sta.Text = stations(stationCurrent).sta
		show_cnt.Text = stations(stationCurrent).cnt
		show_lat.Text = stations(stationCurrent).lat
		show_lon.Text = stations(stationCurrent).lon
		show_notes.Text = stations(stationCurrent).notes
		fra_station_show.Text = "Station " & (stationCurrent + 1) & " of " & (stationsCount + 1) & " (Display Mode)"
		stationLoaded = True
		Call edit()
	End Sub
	
	'UPGRADE_NOTE: scroll_station.Change was changed from an event to a procedure. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2010"'
	'UPGRADE_WARNING: HScrollBar event scroll_station.Change has a new behavior. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2065"'
	Private Sub scroll_station_Change(ByVal newScrollValue As Integer)
		stationCurrent = newScrollValue
		Call update_station()
		Call update_status()
	End Sub
	'UPGRADE_NOTE: scroll_station.Scroll was changed from an event to a procedure. Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup2010"'
	Private Sub scroll_station_Scroll_Renamed(ByVal newScrollValue As Integer)
		stationCurrent = newScrollValue
		Call update_station()
		Call update_status()
	End Sub
	Sub update_coords()
		Dim lat, sign, lon As String
		Dim min, deg, sec As Short
		Dim startPos, endPos As Short
		Dim temp As String
		
		' Has any value been given?
		If (edit_lat.Text & edit_lon.Text) = "" Then
			Exit Sub
		End If
		
		'See if user swapped values
		If ((VB.Right(edit_lat.Text, 1) = "E" Or VB.Right(edit_lat.Text, 1) = "W")) Then
			Call lat_error()
			Exit Sub
		End If
		If ((VB.Right(edit_lon.Text, 1) = "N" Or VB.Right(edit_lon.Text, 1) = "S")) Then
			Call lon_error()
			Exit Sub
		End If
		
		' See if these are WWSU coordinates
		If ((VB.Right(edit_lat.Text, 1) = "N" Or VB.Right(edit_lat.Text, 1) = "S") And (VB.Right(edit_lon.Text, 1) = "E" Or VB.Right(edit_lon.Text, 1) = "W")) Then
			temp = edit_lat.Text
			startPos = 1
			endPos = InStr(startPos, temp, ".")
			If (endPos = 0) Then
				Call lat_error()
				Exit Sub
			End If
			deg = Val(Mid(temp, startPos, endPos - startPos))
			
			startPos = endPos + 1
			endPos = InStr(startPos, temp, ".")
			If (endPos = 0) Then
				Call lat_error()
				Exit Sub
			End If
			min = Val(Mid(temp, startPos, endPos - startPos))
			
			startPos = endPos + 1
			endPos = InStr(startPos, temp, ".")
			If (endPos = 0) Then
				Call lat_error()
				Exit Sub
			End If
			sec = Val(Mid(temp, startPos, endPos - startPos))
			
			startPos = endPos + 1
			sign = VB.Right(temp, 1)
			
			If (sign <> "N" And sign <> "S") Then
				Call lat_error()
				Exit Sub
			End If
			lat = CStr(deg + (CShort(((Val(CStr(min)) / 60) + (Val(CStr(sec / 3600)))) * 10000) / 10000))
			If (sign = "S") Then
				lat = CStr(CDbl(lat) * -1)
			End If
			
			temp = edit_lon.Text
			startPos = 1
			endPos = InStr(startPos, temp, ".")
			If (endPos = 0) Then
				Call lon_error()
				Exit Sub
			End If
			deg = Val(Mid(temp, startPos, endPos - startPos))
			
			startPos = endPos + 1
			endPos = InStr(startPos, temp, ".")
			If (endPos = 0) Then
				Call lon_error()
				Exit Sub
			End If
			min = Val(Mid(temp, startPos, endPos - startPos))
			
			startPos = endPos + 1
			endPos = InStr(startPos, temp, ".")
			If (endPos = 0) Then
				Call lon_error()
				Exit Sub
			End If
			sec = Val(Mid(temp, startPos, endPos - startPos))
			
			startPos = endPos + 1
			sign = VB.Right(temp, 1)
			
			If (sign <> "E" And sign <> "W") Then
				Call lon_error()
				Exit Sub
			End If
			lon = CStr(deg + (CShort(((Val(CStr(min)) / 60) + (Val(CStr(sec / 3600)))) * 10000) / 10000))
			If (sign = "W") Then
				lon = CStr(CDbl(lon) * -1)
			End If
			
			edit_lat.Text = lat
			edit_lon.Text = lon
		Else
			If (Val(edit_lat.Text) = 0) Then
				Call lat_error()
				Exit Sub
			Else
				edit_lat.Text = CStr(Val(edit_lat.Text))
			End If
			If (Val(edit_lon.Text) = 0) Then
				Call lon_error()
				Exit Sub
			Else
				edit_lon.Text = CStr(Val(edit_lon.Text))
			End If
		End If
	End Sub
	Sub lat_error()
		'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
		MsgBox("Sorry - I can't understand this latitude." & Chr(13) & "Acceptable formats are Decimal or WWSU latitude:" & Chr(13) & "  DD.NNNN or" & Chr(13) & "  DD.MM.SS.H (where H is N or S).", MsgBoxStyle.Exclamation + MsgBoxStyle.OKOnly, get_name())
		edit_lat.Text = ""
	End Sub
	Sub lon_error()
		'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
		MsgBox("Sorry - I can't understand this longitude." & Chr(13) & "Acceptable formats are Decimal or WWSU longitude:" & Chr(13) & "  DD.NNNN or" & Chr(13) & "  DDD.MM.SS.H (where H is E or W).", MsgBoxStyle.Exclamation + MsgBoxStyle.OKOnly, get_name())
		edit_lon.Text = ""
	End Sub
	Private Sub scroll_station_Scroll(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.ScrollEventArgs) Handles scroll_station.Scroll
		Select Case eventArgs.type
			Case System.Windows.Forms.ScrollEventType.ThumbTrack
				scroll_station_Scroll_Renamed(eventArgs.newValue)
			Case System.Windows.Forms.ScrollEventType.EndScroll
				scroll_station_Change(eventArgs.newValue)
		End Select
	End Sub

End Class