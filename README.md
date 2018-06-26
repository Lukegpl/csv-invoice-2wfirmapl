# Uploader pozycji do faktury w postci csv do wFirma.pl

Prosty uploader produktów do stworzonej faktury w wFirma.pl.
W katalogu config ustawiamy usera oraz hasło. Dodatkowo w określamy w których kolumnach ładowanych dokumentów znajduje się cena, ilość szt.
oraz kod produktu. 

Warunek jest taki, że musi być utworzona faktura w wFirma.pl. Po utworzeniu pustego dokumentu wFirma możemy zuplodować całą listę
produktów do faktury. Wychodząc oczywiście z założenia, że produkty znajdują się już w kartotece wFirma. 

W katalogu public mamy uploader. W katalogu config pliczek settings.ini z ustawiniami oraz src malutkie źródła projektu.
