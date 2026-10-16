from pathlib import Path
import re, html
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle, KeepTogether
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.colors import HexColor
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm

root=Path(__file__).resolve().parent.parent
out=root/'output/pdf'; out.mkdir(parents=True,exist_ok=True)
for name,file in [('Arial','arial.ttf'),('Arial-Bold','arialbd.ttf'),('Arial-Italic','ariali.ttf'),('Arial-BoldItalic','arialbi.ttf')]:
    pdfmetrics.registerFont(TTFont(name,str(Path('C:/Windows/Fonts')/file)))
pdfmetrics.registerFontFamily('Arial',normal='Arial',bold='Arial-Bold',italic='Arial-Italic',boldItalic='Arial-BoldItalic')
ink=HexColor('#123744'); cyan=HexColor('#0B8EB7'); grey=HexColor('#60747D')
styles={
 'body':ParagraphStyle('body',fontName='Arial',fontSize=9.5,leading=14,spaceAfter=7,textColor=ink),
 'guide':ParagraphStyle('guide',fontName='Arial',fontSize=9,leading=13,spaceAfter=5,textColor=ink),
 'h1':ParagraphStyle('h1',fontName='Arial-Bold',fontSize=23,leading=29,spaceAfter=16,textColor=ink,keepWithNext=True),
 'h2':ParagraphStyle('h2',fontName='Arial-Bold',fontSize=14,leading=19,spaceBefore=14,spaceAfter=9,textColor=cyan,keepWithNext=True),
 'field':ParagraphStyle('field',fontName='Arial-Bold',fontSize=10.5,leading=15,spaceBefore=10,spaceAfter=6,textColor=ink,keepWithNext=True),
 'code':ParagraphStyle('code',fontName='Arial',fontSize=9.5,leading=14,textColor=HexColor('#122F3A')),
 'cell':ParagraphStyle('cell',fontName='Arial',fontSize=7.7,leading=11,textColor=ink),
 'note':ParagraphStyle('note',fontName='Arial-Italic',fontSize=9,leading=13,spaceAfter=7,textColor=grey),
}
width=A4[0]-36*mm
def inline(s):
    s=s.replace('—','-').replace('–','-').replace('\u2011','-').replace('\u00a0',' ')
    s=html.escape(s)
    def link(m):
        target=m[2]
        if not target.startswith('http'):
            model=re.search(r'star-(\d{4}-\d+)-kv',target)
            target='#model-'+model.group(1) if model else '#guide'
        return '<link href="'+target+'" color="#087FA3">'+m[1]+'</link>'
    s=re.sub(r'\[([^\]]+)\]\(([^)]+)\)',link,s)
    s=re.sub(r'\*\*(.+?)\*\*',r'<b>\1</b>',s)
    s=re.sub(r'`([^`]+)`',r'<b>\1</b>',s)
    return s
story=[]; lines=(root/'output/product-transfer/all-products.md').read_text(encoding='utf8').splitlines(); i=0
while i<len(lines):
    s=lines[i].strip()
    if not s or s=='---': i+=1;continue
    if s.startswith('# '):
        if story: story.append(PageBreak())
        story.append(Paragraph(inline(s[2:]),styles['h1']));i+=1;continue
    if s.startswith('## '):story.append(Paragraph(inline(s[3:]),styles['h2']));i+=1;continue
    if re.fullmatch(r'\*\*.*\*\*',s):story.append(Paragraph(inline(s[2:-2]),styles['field']));i+=1;continue
    if s.startswith('```'):
        raw=[];i+=1
        while i<len(lines) and not lines[i].strip().startswith('```'):raw.append(lines[i]);i+=1
        content=Paragraph('<br/>'.join(inline(x) for x in raw),styles['code'])
        box=Table([[content]],colWidths=[width],style=TableStyle([('BACKGROUND',(0,0),(-1,-1),HexColor('#F1F7FA')),('BOX',(0,0),(-1,-1),.4,HexColor('#D6E5EC')),('LEFTPADDING',(0,0),(-1,-1),10),('RIGHTPADDING',(0,0),(-1,-1),10),('TOPPADDING',(0,0),(-1,-1),8),('BOTTOMPADDING',(0,0),(-1,-1),8)]))
        story.extend([box,Spacer(1,7)]);i+=1;continue
    if s.startswith('|'):
        rows=[]
        while i<len(lines) and lines[i].strip().startswith('|'):
            cells=[c.strip() for c in lines[i].strip().strip('|').split('|')]
            if not all(re.fullmatch(r':?-+:?',c) for c in cells):rows.append(cells)
            i+=1
        n=len(rows[0]); weights=[1]*n
        if n==7:weights=[1.1,1,1,1,1.15,1.45,1]
        table=Table([[Paragraph(inline(c),styles['cell']) for c in row] for row in rows],colWidths=[width*w/sum(weights) for w in weights],repeatRows=1,hAlign='LEFT')
        table.setStyle(TableStyle([('VALIGN',(0,0),(-1,-1),'TOP'),('BACKGROUND',(0,0),(-1,0),HexColor('#DDF0F7')),('ROWBACKGROUNDS',(0,1),(-1,-1),[HexColor('#FFFFFF'),HexColor('#F5F9FB')]),('GRID',(0,0),(-1,-1),.35,HexColor('#D5E3E9')),('LEFTPADDING',(0,0),(-1,-1),5),('RIGHTPADDING',(0,0),(-1,-1),5),('TOPPADDING',(0,0),(-1,-1),6),('BOTTOMPADDING',(0,0),(-1,-1),6)]))
        story.extend([table,Spacer(1,10)]);continue
    if s.startswith('_') and s.endswith('_'):story.append(Paragraph(inline(s[1:-1]),styles['note']));i+=1;continue
    parts=[s];i+=1
    if not s.startswith('- ') and not re.match(r'^\d+\.',s):
        while i<len(lines) and lines[i].strip() and not lines[i].startswith(('#','|','```','**','_')):parts.append(lines[i].strip());i+=1
    story.append(Paragraph(inline(' '.join(parts)),styles['body'] if any(isinstance(f,Paragraph) and f.style.name=='h1' and f.getPlainText().startswith('STAR ') for f in story) else styles['guide']))

grouped=[]; pending=[]
for flowable in story:
    heading=isinstance(flowable,Paragraph) and flowable.style.name in ('field','h1','h2')
    if pending and (heading or isinstance(flowable,PageBreak)):
        if grouped and isinstance(grouped[-1],Paragraph) and grouped[-1].style.name=='h2':pending.insert(0,grouped.pop())
        grouped.append(KeepTogether(pending));pending=[]
    if isinstance(flowable,Paragraph) and flowable.style.name=='field':pending=[flowable]
    elif pending:pending.append(flowable)
    else:grouped.append(flowable)
if pending:
    if grouped and isinstance(grouped[-1],Paragraph) and grouped[-1].style.name=='h2':pending.insert(0,grouped.pop())
    grouped.append(KeepTogether(pending))

def page(canvas,doc):
    canvas.saveState();canvas.setStrokeColor(HexColor('#D6E5EC'));canvas.line(18*mm,18*mm,A4[0]-18*mm,18*mm)
    canvas.setFont('Arial',8);canvas.setFillColor(grey);canvas.drawString(18*mm,12*mm,'MotorSky | Заполнение товаров WordPress');canvas.drawRightString(A4[0]-18*mm,12*mm,str(doc.page));canvas.restoreState()
class PDFDoc(SimpleDocTemplate):
    def afterFlowable(self,flowable):
        if isinstance(flowable,Paragraph) and flowable.style.name=='h1':
            label=flowable.getPlainText(); model=re.search(r'STAR (\d{4}) - (\d+) KV',label)
            key='model-'+model.group(1)+'-'+model.group(2) if model else 'guide'
            self.canv.bookmarkPage(key); self.canv.addOutlineEntry(label,key,0)
file=out/'MotorSky-all-products.pdf'
doc=PDFDoc(str(file),pagesize=A4,rightMargin=18*mm,leftMargin=18*mm,topMargin=18*mm,bottomMargin=24*mm,title='MotorSky - заполнение всех товаров WordPress',author='MotorSky')
doc.build(grouped,onFirstPage=page,onLaterPages=page)
print(file)
