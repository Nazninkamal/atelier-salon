#!/usr/bin/env python3
"""Build deterministic installable ZIPs and a buyer bundle. Run at repository root."""
from pathlib import Path
from tempfile import TemporaryDirectory
import zipfile,hashlib,json
repo=Path(__file__).resolve().parents[1]
source=repo/'source' if (repo/'source/atelier-salon').is_dir() else repo
out=repo/'dist';out.mkdir(exist_ok=True)
def archive(folder,path,prefix=''):
 with zipfile.ZipFile(path,'w',zipfile.ZIP_DEFLATED,9) as z:
  for p in sorted(folder.rglob('*')):
   if not p.is_file() or p.name in ['.DS_Store'] or '__pycache__' in p.parts:continue
   name=prefix+str(p.relative_to(folder));info=zipfile.ZipInfo(name,(2026,11,8,0,0,0));info.compress_type=zipfile.ZIP_DEFLATED;info.external_attr=0o100644<<16;z.writestr(info,p.read_bytes())
packages={'atelier-salon':'3.1.0','atelier-salon-child':'1.1.0','atelier-appointments':'1.1.0'}
with TemporaryDirectory() as tmp:
 bundle=Path(tmp)
 for name,version in packages.items():
  path=out/(name+'-'+version+'.zip');archive(source/name,path,name+'/');(bundle/(name+'.zip')).write_bytes(path.read_bytes())
 for p in (repo/'docs').iterdir():
  if p.is_file():(bundle/p.name).write_bytes(p.read_bytes())
 archive(bundle,out/'atelier-commercial-package-3.1.0.zip')
sums=[]
for p in sorted(out.glob('*.zip')):
 with zipfile.ZipFile(p) as z:assert z.testzip() is None
 sums.append(hashlib.sha256(p.read_bytes()).hexdigest()+'  '+p.name)
(out/'SHA256SUMS').write_text('\n'.join(sums)+'\n')
print('\n'.join(sums))
