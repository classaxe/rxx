Option Strict Off
Option Explicit On
Friend Class frm_about
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
	Public WithEvents cmd_ok As System.Windows.Forms.Button
	Public WithEvents Label4 As System.Windows.Forms.Label
	Public WithEvents Label3 As System.Windows.Forms.Label
	Public WithEvents Frame2 As System.Windows.Forms.GroupBox
	Public WithEvents Command1 As System.Windows.Forms.Button
	Public WithEvents Label5 As System.Windows.Forms.Label
	Public WithEvents Label2 As System.Windows.Forms.Label
	Public WithEvents Label1 As System.Windows.Forms.Label
	Public WithEvents Frame1 As System.Windows.Forms.GroupBox
	'NOTE: The following procedure is required by the Windows Form Designer
	'It can be modified using the Windows Form Designer.
	'Do not modify it using the code editor.
	<System.Diagnostics.DebuggerStepThrough()> Private Sub InitializeComponent()
    Me.components = New System.ComponentModel.Container
    Dim resources As System.Resources.ResourceManager = New System.Resources.ResourceManager(GetType(frm_about))
    Me.ToolTip1 = New System.Windows.Forms.ToolTip(Me.components)
    Me.cmd_ok = New System.Windows.Forms.Button
    Me.Frame2 = New System.Windows.Forms.GroupBox
    Me.Label4 = New System.Windows.Forms.Label
    Me.Label3 = New System.Windows.Forms.Label
    Me.Command1 = New System.Windows.Forms.Button
    Me.Frame1 = New System.Windows.Forms.GroupBox
    Me.Label5 = New System.Windows.Forms.Label
    Me.Label2 = New System.Windows.Forms.Label
    Me.Label1 = New System.Windows.Forms.Label
    Me.Frame2.SuspendLayout()
    Me.Frame1.SuspendLayout()
    Me.SuspendLayout()
    '
    'cmd_ok
    '
    Me.cmd_ok.BackColor = System.Drawing.SystemColors.Control
    Me.cmd_ok.Cursor = System.Windows.Forms.Cursors.Default
    Me.cmd_ok.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.cmd_ok.ForeColor = System.Drawing.SystemColors.ControlText
    Me.cmd_ok.Location = New System.Drawing.Point(152, 248)
    Me.cmd_ok.Name = "cmd_ok"
    Me.cmd_ok.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.cmd_ok.Size = New System.Drawing.Size(97, 25)
    Me.cmd_ok.TabIndex = 6
    Me.cmd_ok.Text = "OK"
    '
    'Frame2
    '
    Me.Frame2.BackColor = System.Drawing.Color.FromArgb(CType(160, Byte), CType(255, Byte), CType(160, Byte))
    Me.Frame2.Controls.Add(Me.Label4)
    Me.Frame2.Controls.Add(Me.Label3)
    Me.Frame2.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Frame2.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Frame2.Location = New System.Drawing.Point(8, 152)
    Me.Frame2.Name = "Frame2"
    Me.Frame2.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Frame2.Size = New System.Drawing.Size(401, 89)
    Me.Frame2.TabIndex = 4
    Me.Frame2.TabStop = False
    Me.Frame2.Text = "WWSU NDB Database"
    '
    'Label4
    '
    Me.Label4.BackColor = System.Drawing.Color.Transparent
    Me.Label4.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label4.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label4.ForeColor = System.Drawing.SystemColors.WindowText
    Me.Label4.Location = New System.Drawing.Point(16, 24)
    Me.Label4.Name = "Label4"
    Me.Label4.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label4.Size = New System.Drawing.Size(369, 20)
    Me.Label4.TabIndex = 7
    Me.Label4.Text = "Data from WWSU may be pasted directly into the fields of this tool."
    '
    'Label3
    '
    Me.Label3.BackColor = System.Drawing.Color.Transparent
    Me.Label3.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label3.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label3.ForeColor = System.Drawing.SystemColors.WindowText
    Me.Label3.Location = New System.Drawing.Point(16, 43)
    Me.Label3.Name = "Label3"
    Me.Label3.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label3.Size = New System.Drawing.Size(369, 44)
    Me.Label3.TabIndex = 5
    Me.Label3.Text = "The WWSU NBD Database is highly recommended for anyone with an interest in NBD DX" & _
    "ing.    It may be obtained from Alex Wiecek's web site at http://members.rogers." & _
    "com/wiecek6010"
    '
    'Command1
    '
    Me.Command1.BackColor = System.Drawing.SystemColors.Control
    Me.Command1.Cursor = System.Windows.Forms.Cursors.Default
    Me.Command1.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Command1.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Command1.Location = New System.Drawing.Point(152, 160)
    Me.Command1.Name = "Command1"
    Me.Command1.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Command1.Size = New System.Drawing.Size(113, 25)
    Me.Command1.TabIndex = 3
    Me.Command1.Text = "OK"
    '
    'Frame1
    '
    Me.Frame1.BackColor = System.Drawing.Color.FromArgb(CType(160, Byte), CType(255, Byte), CType(160, Byte))
    Me.Frame1.Controls.Add(Me.Label5)
    Me.Frame1.Controls.Add(Me.Label2)
    Me.Frame1.Controls.Add(Me.Label1)
    Me.Frame1.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Frame1.ForeColor = System.Drawing.SystemColors.ControlText
    Me.Frame1.Location = New System.Drawing.Point(8, 8)
    Me.Frame1.Name = "Frame1"
    Me.Frame1.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Frame1.Size = New System.Drawing.Size(401, 145)
    Me.Frame1.TabIndex = 0
    Me.Frame1.TabStop = False
    Me.Frame1.Text = "NDB Weblog Editor"
    '
    'Label5
    '
    Me.Label5.BackColor = System.Drawing.Color.Transparent
    Me.Label5.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label5.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label5.ForeColor = System.Drawing.SystemColors.WindowText
    Me.Label5.Location = New System.Drawing.Point(16, 104)
    Me.Label5.Name = "Label5"
    Me.Label5.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label5.Size = New System.Drawing.Size(369, 33)
    Me.Label5.TabIndex = 8
    Me.Label5.Text = "You can include the file path in the command line when you call this program. Use" & _
    " Ctrl+S to save changes"
    '
    'Label2
    '
    Me.Label2.BackColor = System.Drawing.Color.Transparent
    Me.Label2.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label2.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label2.ForeColor = System.Drawing.SystemColors.WindowText
    Me.Label2.Location = New System.Drawing.Point(16, 56)
    Me.Label2.Name = "Label2"
    Me.Label2.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label2.Size = New System.Drawing.Size(369, 49)
    Me.Label2.TabIndex = 2
    Me.Label2.Text = "This tool is designed to facilitate the editing of the stations.js data file used" & _
    " with that system. If the stations.js file is not found in the same folder as th" & _
    "is program, you will be prompted to specify the folder in which it resides ."
    '
    'Label1
    '
    Me.Label1.BackColor = System.Drawing.Color.Transparent
    Me.Label1.Cursor = System.Windows.Forms.Cursors.Default
    Me.Label1.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Label1.ForeColor = System.Drawing.SystemColors.WindowText
    Me.Label1.Location = New System.Drawing.Point(16, 24)
    Me.Label1.Name = "Label1"
    Me.Label1.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.Label1.Size = New System.Drawing.Size(369, 33)
    Me.Label1.TabIndex = 1
    Me.Label1.Text = "NDB WebLog is a web-based tool for displaying reception logs.   It is available f" & _
    "or free from the author's web site at http://www.classaxe.com/dx"
    '
    'frm_about
    '
    Me.AutoScaleBaseSize = New System.Drawing.Size(5, 13)
    Me.BackColor = System.Drawing.Color.FromArgb(CType(160, Byte), CType(255, Byte), CType(160, Byte))
    Me.ClientSize = New System.Drawing.Size(416, 275)
    Me.Controls.Add(Me.cmd_ok)
    Me.Controls.Add(Me.Frame2)
    Me.Controls.Add(Me.Command1)
    Me.Controls.Add(Me.Frame1)
    Me.Cursor = System.Windows.Forms.Cursors.Default
    Me.Font = New System.Drawing.Font("Arial", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
    Me.Icon = CType(resources.GetObject("$this.Icon"), System.Drawing.Icon)
    Me.Location = New System.Drawing.Point(251, 270)
    Me.Name = "frm_about"
    Me.RightToLeft = System.Windows.Forms.RightToLeft.No
    Me.StartPosition = System.Windows.Forms.FormStartPosition.Manual
    Me.Text = "About..."
    Me.Frame2.ResumeLayout(False)
    Me.Frame1.ResumeLayout(False)
    Me.ResumeLayout(False)

  End Sub
#End Region 
#Region "Upgrade Support "
	Private Shared m_vb6FormDefInstance As frm_about
	Private Shared m_InitializingDefInstance As Boolean
	Public Shared Property DefInstance() As frm_about
		Get
			If m_vb6FormDefInstance Is Nothing OrElse m_vb6FormDefInstance.IsDisposed Then
				m_InitializingDefInstance = True
				m_vb6FormDefInstance = New frm_about()
				m_InitializingDefInstance = False
			End If
			DefInstance = m_vb6FormDefInstance
		End Get
		Set
			m_vb6FormDefInstance = Value
		End Set
	End Property
#End Region 
	Private Sub cmd_ok_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmd_ok.Click
		frm_about.DefInstance.Visible = False
	End Sub
	Private Sub frm_about_Load(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles MyBase.Load
		'UPGRADE_WARNING: Couldn't resolve default property of object get_name(). Click for more: 'ms-help://MS.VSCC.2003/commoner/redir/redirect.htm?keyword="vbup1037"'
		frm_about.DefInstance.Text = get_name()
	End Sub
End Class