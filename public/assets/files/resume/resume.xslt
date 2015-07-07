<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<w:document mc:Ignorable="w14 w15 wp14" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" xmlns:w15="http://schemas.microsoft.com/office/word/2012/wordml" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape">
	<w:body>
		<xsl:for-each select="root/row">
		<w:p w:rsidP="00C50A95" w:rsidR="0071790A" w:rsidRDefault="00C50A95">
			<w:pPr>
				<w:pStyle w:val="Title"/>
				<w:jc w:val="center"/>
			</w:pPr>
			<w:r>
				<w:t><xsl:value-of select="name"/></w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00C50A95">
			<w:pPr>
				<w:jc w:val="center"/>
			</w:pPr>
			<w:r>
				<w:t><xsl:value-of select="email"/> | <xsl:value-of select="phone"/> | <xsl:value-of select="city"/></w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00C50A95" w:rsidRPr="000D0693">
			<w:pPr>
				<w:pStyle w:val="Heading1"/>
				<w:rPr>
					<w:b/>
				</w:rPr>
			</w:pPr>
			<w:r w:rsidRPr="000D0693">
				<w:rPr>
					<w:b/>
				</w:rPr>
				<w:t>Objective</w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00A477AE" w:rsidRPr="00C50A95">
			<w:pPr>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
				<w:pict>
					<v:rect fillcolor="#a0a0a0" id="_x0000_i1025" o:hr="t" o:hralign="center" o:hrstd="t" stroked="f" style="width:0;height:1.5pt"/>
				</w:pict>
			</w:r>
		</w:p>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00C50A95" w:rsidRPr="000D0693">
			<w:pPr>
				<w:rPr>
					<w:sz w:val="24"/>
				</w:rPr>
			</w:pPr>
			<w:r w:rsidRPr="000D0693">
				<w:rPr>
					<w:sz w:val="24"/>
				</w:rPr>
				<w:t><xsl:value-of select="about"/></w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00C50A95" w:rsidRPr="000D0693">
			<w:pPr>
				<w:pStyle w:val="Heading1"/>
				<w:rPr>
					<w:b/>
				</w:rPr>
			</w:pPr>
			<w:r w:rsidRPr="000D0693">
				<w:rPr>
					<w:b/>
				</w:rPr>
				<w:t>Education</w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00A477AE">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
				<w:pict>
					<v:rect fillcolor="#a0a0a0" id="_x0000_i1026" o:hr="t" o:hralign="center" o:hrstd="t" stroked="f" style="width:0;height:1.5pt"/>
				</w:pict>
			</w:r>
		</w:p>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00C50A95">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
		</w:p>
		<w:tbl>
			<w:tblPr>
				<w:tblStyle w:val="PlainTable4"/>
				<w:tblW w:type="auto" w:w="0"/>
				<w:tblLook w:firstColumn="1" w:firstRow="1" w:lastColumn="0" w:lastRow="0" w:noHBand="0" w:noVBand="1" w:val="04A0"/>
			</w:tblPr>
			<w:tblGrid>
				<w:gridCol w:w="1870"/>
				<w:gridCol w:w="1870"/>
				<w:gridCol w:w="1870"/>
				<w:gridCol w:w="1870"/>
				<w:gridCol w:w="1870"/>
			</w:tblGrid>
			<w:tr w:rsidR="00C50A95" w:rsidRPr="000D0693" w:rsidTr="001F5CE5">
				<w:trPr>
					<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="1" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="0" w:oddVBand="0" w:val="100000000000"/>
				</w:trPr>
				<w:tc>
					<w:tcPr>
						<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="1" w:firstRow="0" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="0" w:oddVBand="0" w:val="001000000000"/>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5" w:rsidRPr="000D0693">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
						</w:pPr>
						<w:r w:rsidRPr="000D0693">
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
							<w:t>Institute</w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5" w:rsidRPr="000D0693">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="1" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="0" w:oddVBand="0" w:val="100000000000"/>
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
						</w:pPr>
						<w:r w:rsidRPr="000D0693">
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
							<w:t>Major</w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5" w:rsidRPr="000D0693">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="1" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="0" w:oddVBand="0" w:val="100000000000"/>
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
						</w:pPr>
						<w:r w:rsidRPr="000D0693">
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
							<w:t>Degree</w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5" w:rsidRPr="000D0693">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="1" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="0" w:oddVBand="0" w:val="100000000000"/>
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
						</w:pPr>
						<w:r w:rsidRPr="000D0693">
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
							<w:t>Year</w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5" w:rsidRPr="000D0693">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="1" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="0" w:oddVBand="0" w:val="100000000000"/>
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
						</w:pPr>
						<w:r w:rsidRPr="000D0693">
							<w:rPr>
								<w:b w:val="0"/>
								<w:sz w:val="26"/>
								<w:szCs w:val="26"/>
							</w:rPr>
							<w:t>GPA</w:t>
						</w:r>
					</w:p>
				</w:tc>
			</w:tr>
			<xsl:for-each select="educations/education">
			<w:tr w:rsidR="00C50A95" w:rsidTr="001F5CE5">
				<w:trPr>
					<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="0" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="1" w:oddVBand="0" w:val="000000100000"/>
				</w:trPr>
				<w:tc>
					<w:tcPr>
						<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="1" w:firstRow="0" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="0" w:oddVBand="0" w:val="001000000000"/>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5">
						<w:r>
							<w:t><xsl:value-of select="institute"/></w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="0" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="1" w:oddVBand="0" w:val="000000100000"/>
						</w:pPr>
						<w:r>
							<w:t><xsl:value-of select="major"/></w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="0" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="1" w:oddVBand="0" w:val="000000100000"/>
						</w:pPr>
						<w:r>
							<w:t><xsl:value-of select="degree"/></w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="0" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="1" w:oddVBand="0" w:val="000000100000"/>
						</w:pPr>
						<w:r>
							<w:t><xsl:value-of select="year"/></w:t>
						</w:r>
					</w:p>
				</w:tc>
				<w:tc>
					<w:tcPr>
						<w:tcW w:type="dxa" w:w="1870"/>
					</w:tcPr>
					<w:p w:rsidP="001F5CE5" w:rsidR="00C50A95" w:rsidRDefault="001F5CE5">
						<w:pPr>
							<w:jc w:val="center"/>
							<w:cnfStyle w:evenHBand="0" w:evenVBand="0" w:firstColumn="0" w:firstRow="0" w:firstRowFirstColumn="0" w:firstRowLastColumn="0" w:lastColumn="0" w:lastRow="0" w:lastRowFirstColumn="0" w:lastRowLastColumn="0" w:oddHBand="1" w:oddVBand="0" w:val="000000100000"/>
						</w:pPr>
						<w:r>
							<w:t><xsl:value-of select="gpa"/></w:t>
						</w:r>
					</w:p>
				</w:tc>
			</w:tr>
			</xsl:for-each>
		</w:tbl>
		<w:p w:rsidP="00C50A95" w:rsidR="00C50A95" w:rsidRDefault="00C50A95"/>
		<w:p w:rsidP="001F5CE5" w:rsidR="001F5CE5" w:rsidRDefault="001F5CE5" w:rsidRPr="000D0693">
			<w:pPr>
				<w:pStyle w:val="Heading1"/>
				<w:rPr>
					<w:b/>
				</w:rPr>
			</w:pPr>
			<w:r w:rsidRPr="000D0693">
				<w:rPr>
					<w:b/>
				</w:rPr>
				<w:t>Work Experience</w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="001F5CE5" w:rsidR="001F5CE5" w:rsidRDefault="00A477AE">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
				<w:pict>
					<v:rect fillcolor="#a0a0a0" id="_x0000_i1027" o:hr="t" o:hralign="center" o:hrstd="t" stroked="f" style="width:0;height:1.5pt"/>
				</w:pict>
			</w:r>
		</w:p>
		<w:p w:rsidP="001F5CE5" w:rsidR="001F5CE5" w:rsidRDefault="001F5CE5">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
		</w:p>
		<xsl:for-each select="works/work">
		<w:p w:rsidP="001F5CE5" w:rsidR="001F5CE5" w:rsidRDefault="001F5CE5">
			<w:pPr>
				<w:pStyle w:val="ListParagraph"/>
				<w:numPr>
					<w:ilvl w:val="0"/>
					<w:numId w:val="1"/>
				</w:numPr>
			</w:pPr>
			<w:r>
				<w:t><xsl:value-of select="position"/> at <xsl:value-of select="company"/> from <xsl:value-of select="duration"/>: <xsl:value-of select="responsibility"/></w:t>
			</w:r>
		</w:p>
		</xsl:for-each>
		<w:p w:rsidP="00A477AE" w:rsidR="00A477AE" w:rsidRDefault="00A477AE" w:rsidRPr="000D0693">
			<w:pPr>
				<w:pStyle w:val="Heading1"/>
				<w:rPr>
					<w:b/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:b/>
				</w:rPr>
				<w:t>Skills</w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00A477AE" w:rsidR="00A477AE" w:rsidRDefault="00A477AE">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
				<w:pict>
					<v:rect fillcolor="#a0a0a0" id="_x0000_i1030" o:hr="t" o:hralign="center" o:hrstd="t" stroked="f" style="width:0;height:1.5pt"/>
				</w:pict>
			</w:r>
		</w:p>
		<w:p w:rsidP="00A477AE" w:rsidR="00A477AE" w:rsidRDefault="00A477AE">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
		</w:p>
		<w:p w:rsidP="00A477AE" w:rsidR="00A477AE" w:rsidRDefault="00A477AE" w:rsidRPr="00A477AE">
			<w:r>
				<w:t><xsl:value-of select="skills"/></w:t>
			</w:r>
			<w:bookmarkStart w:id="0" w:name="_GoBack"/>
			<w:bookmarkEnd w:id="0"/>
		</w:p>
		<w:p w:rsidP="00A36CDB" w:rsidR="00A36CDB" w:rsidRDefault="00A36CDB" w:rsidRPr="000D0693">
			<w:pPr>
				<w:pStyle w:val="Heading1"/>
				<w:rPr>
					<w:b/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:b/>
				</w:rPr>
				<w:t>Achievements</w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00A36CDB" w:rsidR="00A36CDB" w:rsidRDefault="00A477AE">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
				<w:pict>
					<v:rect fillcolor="#a0a0a0" id="_x0000_i1028" o:hr="t" o:hralign="center" o:hrstd="t" stroked="f" style="width:0;height:1.5pt"/>
				</w:pict>
			</w:r>
		</w:p>
		<w:p w:rsidP="00A36CDB" w:rsidR="00A36CDB" w:rsidRDefault="00A36CDB">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
		</w:p>
		<w:p w:rsidP="00A36CDB" w:rsidR="00A36CDB" w:rsidRDefault="00A36CDB">
			<w:r>
				<w:t>List your achievements</w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00784162" w:rsidR="00784162" w:rsidRDefault="00784162" w:rsidRPr="000D0693">
			<w:pPr>
				<w:pStyle w:val="Heading1"/>
				<w:rPr>
					<w:b/>
				</w:rPr>
			</w:pPr>
			<w:r w:rsidRPr="000D0693">
				<w:rPr>
					<w:b/>
				</w:rPr>
				<w:t>Hobbies</w:t>
			</w:r>
		</w:p>
		<w:p w:rsidP="00784162" w:rsidR="00784162" w:rsidRDefault="00A477AE">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
				<w:pict>
					<v:rect fillcolor="#a0a0a0" id="_x0000_i1029" o:hr="t" o:hralign="center" o:hrstd="t" stroked="f" style="width:0;height:1.5pt"/>
				</w:pict>
			</w:r>
		</w:p>
		<w:p w:rsidP="00784162" w:rsidR="00784162" w:rsidRDefault="00784162">
			<w:pPr>
				<w:pStyle w:val="NoSpacing"/>
				<w:rPr>
					<w:sz w:val="2"/>
				</w:rPr>
			</w:pPr>
		</w:p>
		<w:p w:rsidP="00784162" w:rsidR="001F5CE5" w:rsidRDefault="000D0693" w:rsidRPr="00C50A95">
			<w:r>
				<w:t>Describe Your Hobbies</w:t>
			</w:r>
		</w:p>
		</xsl:for-each>
		<w:sectPr w:rsidR="001F5CE5" w:rsidRPr="00C50A95">
			<w:pgSz w:h="15840" w:w="12240"/>
			<w:pgMar w:bottom="1440" w:footer="720" w:gutter="0" w:header="720" w:left="1440" w:right="1440" w:top="1440"/>
			<w:cols w:space="720"/>
			<w:docGrid w:linePitch="360"/>
		</w:sectPr>
	</w:body>
</w:document>
</xsl:template>
</xsl:stylesheet>