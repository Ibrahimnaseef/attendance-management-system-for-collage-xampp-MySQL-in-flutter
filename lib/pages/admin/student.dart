import 'package:flutter/material.dart';

class FacultyStudentsPage extends StatelessWidget {
  final List<String> departments = [
    "COMPUTER SCIENCE AND ENGINEERING",
    "CYBER SECURITY",
    "POLYMER ENGINEERING",
    "ELECTRONICS AND COMMUNICATION",
    "ELECTRICAL AND ELECTRONICS",
  ];

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          backgroundColor: Colors.green[100],
          elevation: 0,
          title: Text("Show up design", style: TextStyle(color: Colors.black)),
          centerTitle: true,
          leading: IconButton(
            icon: Icon(Icons.menu, color: Colors.black),
            onPressed: () {},
          ),
          actions: [
            IconButton(
              icon: Icon(Icons.refresh, color: Colors.black),
              onPressed: () {},
            ),
          ],
          bottom: TabBar(
            labelColor: Colors.black,
            unselectedLabelColor: Colors.grey,
            indicatorColor: Colors.black,
            tabs: [Tab(text: "Faculty"), Tab(text: "Students")],
          ),
        ),
        body: TabBarView(
          children: [
            FacultyTab(departments: departments),
            Center(child: Text("Students List")),
          ],
        ),
      ),
    );
  }
}

class FacultyTab extends StatelessWidget {
  final List<String> departments;

  FacultyTab({required this.departments});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.green[50],
      padding: EdgeInsets.all(16.0),
      child: ListView.builder(
        itemCount: departments.length,
        itemBuilder: (context, index) {
          return Card(
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(15.0),
            ),
            elevation: 2,
            child: Padding(
              padding: EdgeInsets.all(16.0),
              child: Text(
                departments[index],
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
            ),
          );
        },
      ),
    );
  }
}
