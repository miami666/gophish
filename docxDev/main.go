package main

import (
	"github.com/nguyenthenguyen/docx"
)

func main() {
// Read from docx file
//r, err := docx.ReadDocxFile("./template.docx")
// Or read from memory
// r, err := docx.ReadDocxFromMemory(data io.ReaderAt, size int64)
//if err != nil {
//panic(err)


// Replace like https://golang.org/pkg/strings/#Replace
//docx1.Replace("Hense", "von und zu", -1)
//docx1.Replace("old_1_2", "new_1_2", -1)
//docx1.ReplaceLink("http://example.com/", "https://github.com/nguyenthenguyen/docx")
/*docx1.ReplaceHeader("out with the old", "in with the new")
docx1.ReplaceFooter("Change This Footer", "new footer")*/
//docx1.WriteToFile("./template.docm")

//docx2 := r.Editable()
//docx2.Replace("old_2_1", "new_2_1", -1)
//docx2.Replace("old_2_2", "new_2_2", -1)
//docx2.WriteToFile("./new_result_2.docx")

//Or write to ioWriter
//docx2.Write(ioWriter io.Writer)

GetDocx("666")
}
func GetDocx(rid string)  {
	r, err := docx.ReadDocxFile("./test.docm")

	if err != nil {
		panic(err)
	}
	docx1 := r.Editable()


	docx1.Replace("Bob", rid, -1)
	docx1.Replace("old_1_2", "new_1_2", -1)
	docx1.WriteToFile("./test1.doc")
	//docx1.SetContent(rid)

	r.Close()

}