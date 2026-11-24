import sys
sys.path.insert(0, 'flask_service')
import fuzzy_engine

scenarios = [
    (20, 2, 2,  "Rp321.000",   "Murah"),
    (50, 5, 5,  "Rp1.025.000", "Sedang"),
    (90, 9, 9,  "Rp1.729.000", "Mahal"),
    (30, 8, 6,  "Rp1.151.000", "Sedang"),
    (80, 3, 8,  "Rp1.701.000", "Mahal"),
    (0,  0, 0,  "Rp321.000",   "Murah"),
    (100,10,10, "Rp1.729.000", "Mahal"),
]

print("=" * 65)
print(f"{'Sk':<4} {'K':>5} {'U':>5} {'D':>5}  {'Estimasi':>14}  {'Kat':<8}  {'PRD':>14}  {'OK'}")
print("=" * 65)

all_ok = True
for i, (k, u, d, prd_biaya, prd_kat) in enumerate(scenarios, 1):
    res  = fuzzy_engine.hitung_estimasi(k, u, d)
    biaya = res['estimasi_biaya']
    kat   = res['kategori_biaya']
    prd_num = int(prd_biaya.replace("Rp","").replace(".",""))
    selisih = abs(biaya - prd_num)
    ok = selisih <= 2000 and kat == prd_kat
    if not ok:
        all_ok = False
    status = "✓" if ok else f"✗ selisih={selisih} kat={kat}"
    print(f"  {i:<3} {k:>5} {u:>5} {d:>5}  Rp{biaya:>12,}  {kat:<8}  {prd_biaya:>14}  {status}")

print("=" * 65)
print("SEMUA SKENARIO LULUS!" if all_ok else "ADA SKENARIO GAGAL!")
