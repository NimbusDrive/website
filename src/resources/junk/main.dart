import "package:flutter/material.dart";

import "package:firebase_core/firebase_core.dart";
import "package:firebase_auth/firebase_auth.dart";
import "firebase_options.dart";

import "package:todo/welcome.dart";
import "package:todo/signup.dart";
import "package:todo/login.dart";
import "package:todo/projects.dart";

void main() async
{
  WidgetsFlutterBinding.ensureInitialized();

  FirebaseApp app = await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );

  await FirebaseAuth.instance.setSettings(
      appVerificationDisabledForTesting: true
  );

  runApp(MaterialApp(
    initialRoute: "/",

    routes:
    {
      "/": (context) => Welcome(),
      "/signup": (context) => SignUp(),
      "/login": (context) => Login(),
      "/home": (context) => Projects(),
    }
  ));
}
