import 'package:flutter/material.dart';
import 'screens/login_screen.dart';
import 'screens/main_screen.dart';
import 'services/api_service.dart';
import 'theme/app_theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final token = await ApiService.getToken();
  runApp(RmihApp(initialToken: token));
}

/// Root Application Widget RMIH Mobile
class RmihApp extends StatelessWidget {
  final String? initialToken;

  const RmihApp({super.key, this.initialToken});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'RMIH Mobile',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: initialToken != null ? const MainScreen() : const LoginScreen(),
    );
  }
}
