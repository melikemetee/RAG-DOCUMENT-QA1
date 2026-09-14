def metni_parcala(metin, parca_boyutu=100, kesisim=20):

    kelimeler = metin.split()

    parcalar = []

    baslangic = 0

    while baslangic < len(kelimeler):

        bitis = baslangic + parca_boyutu

        parca = " ".join(kelimeler[baslangic:bitis])

        if parca:
            parcalar.append(parca)

        baslangic = bitis - kesisim

    return parcalar