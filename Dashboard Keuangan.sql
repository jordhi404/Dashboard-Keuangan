select 
	a.BedID,
	a.RegistrationID,
	a.RegistrationNo,
	a.BedCode,
	a.RoomID,
	a.RoomCode,
	a.RoomName,
	a.GCBedStatus,
	a.BedStatus,
	a.MedicalNo,
	a.PatientName,
	a.BusinessPartnerName,
	a.ParamedicName,
	a.ClassName,
	p.HomeAddress,
	pn.Notes
from vBed a
LEFT JOIN vPatient p on p.MRN=a.MRN
LEFT JOIN PatientNotes pn on pn.MRN=a.MRN
where a.IsDeleted=0
order by a.BedID