# Pregled projekta
Ova dokumentacija opisuje razvoj veb aplikacije namenjene izdavanju stanova i kuća u određenom gradu. Aplikacija razlikuje tri nivoa pristupa: gost, registrovani / prijavljeni korisnik i administrator.

# Nivoi pristupa i dozvole

## Pristup za goste:
Gosti imaju ograničen pristup funkcijama aplikacije. Oni mogu da:
- Pogledaju opšte informacije dostupne na sajtu
- Pregledaju liste nekretnina koje su dostupne za iznajmljivanje
- Koriste detaljne funkcije pretrage i razne filtere da bi pronašli odgovarajuća svojstva
- Registruje se za nalog na sajtu

## Pristup registrovanih/prijavljenih korisnika:
Registrovani ili prijavljeni korisnici imaju pristup poboljšanim funkcijama, uključujući mogućnost:
- Da pogledaju detaljne liste nekretnina za stanove i kuće dostupne za iznajmljivanje
- Sprovedu napredne pretrage koristeći filtere kao što su tip nekretnine, cena, lokacija i period iznajmljivanja da bi pronašli odgovarajuće nekretnine
- Ažuriraju informacije o njihovom profilu, uključujući promenu njihove lozinke, imena, prezimena i broja telefona
- Postave oglase za iznajmljivanje nekretnina, uključujući otpremanje fotografija, navođenje lokacije u gradu, pružanje opisa i postavljanje perioda zakupa i cena
- Zatraže resetovanje lozinke ako su zaboravili lozinku
- Kontaktiraju druge korisnike koji su postavili oglase putem e-pošte

## Administratorski pristup:
Administratori imaju najviši nivo pristupa i kontrole nad aplikacijom. Njihove mogućnosti uključuju:
- Pregledanje, uređivanje i brisanje svih oglasa postavljenih na sajtu
- Odobravanje ili odbijanje oglasa koje su poslali korisnici
- Omogućavanje ili onemogućavanje prikaza određenih reklama
- Davanje ili uskraćivanje mogućnosti korisnicima da se prijave na sistem

## Registracija korisnika i bezbednost:
Registracija korisnika je dizajnirana da bude bezbedna. Proces uključuje slanje veze sa ključem za aktiviranje putem e-pošte kako bi se potvrdio identitet korisnika. Ova veza za aktivaciju se takođe koristi kada korisnici zahtevaju promenu lozinke. Sistem osigurava da je svaka adresa e-pošte jedinstvena, sprečavajući da se više naloga registruje sa istom adresom e-pošte.

## Objavljivanje oglasa i interakcija korisnika:
Registrovani korisnici mogu postavljati oglase za iznajmljivanje nekretnina i kontaktirati druge korisnike koji su postavili oglase. Kada korisnik objavi oglas, on se prvo drži u stanju čekanja dok ga administrator ne odobri. Nakon odobrenja, postaje javno vidljiv na veb stranici. Kada je nekretnina uspešno izdata, korisnik koji je postavio oglas treba da ažurira status nekretnine u "iznajmljen". Ova promena pokreće automatsko obaveštenje putem e-pošte za korisnika koji je zainteresovan za iznajmljivanje nekretnine. Pored toga, korisnici koji postavljaju oglase imaju mogućnost da vode evidenciju o tome kada i kome je imovina data u zakup, uključujući i dan kada je zakupac primljen.

## Administratorske mogućnosti i bezbednost:
Administratori imaju mogućnost upravljanja korisničkim nalozima, uključujući pregled i deblokiranje blokiranih naloga ako je potrebno. Administrativna oblast je obezbeđena korišćenjem PHP sesija kako bi se osiguralo da samo ovlašćeno osoblje može da joj pristupi. Štaviše, sve korisničke lozinke se bezbedno heširaju korišćenjem BCRYPT algoritma radi poboljšanja bezbednosti.

## Zahtevi za bazu podataka:
Aplikacija zahteva bazu podataka pod nazivom "real_estate". Ova baza podataka treba da sadrži tabele koje podržavaju sve neophodne funkcionalnosti projekta, kao što su upravljanje korisnicima, lista nekretnina, objavljivanje oglasa i administrativne radnje.

## Web adresa projektnog zadatka:
https://nda.stud.vts.su.ac.rs/
