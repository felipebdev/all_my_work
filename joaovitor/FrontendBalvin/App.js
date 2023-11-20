import { StatusBar } from 'expo-status-bar';
import { StyleSheet, Text, View } from 'react-native';
import { createNativeStackNavigator} from '@react-navigation/native-stack';
import { NavigationContainer } from '@react-navigation/native';
import SplashScreen from './screens/SplashScreen';
import Login from './screens/Login';
import Menu from './screens/Menu';
import Addpost from './screens/Addpost'
import Registraremail from './screens/Registrar/Registraremail';
import Registrarverificaremail from './screens/Registrar/Registrarverificaremail';
import Registrar from './screens/Registrar/Registrarnovasenha';
import Registrarusername from './screens/Registrar/Registrarusername';
import Registrarnovasenha from './screens/Registrar/Registrarnovasenha';
import Configuracao from './screens/Editarperfil/Configuracao';
import Mudarusername from './screens/Editarperfil/Mudarusername';
import Mudardescricao from './screens/Editarperfil/Mudardescricao';
import Mudarsenha from './screens/Editarperfil/Mudarsenha';
import Outrosusuarios from './screens/Profile/Outrosusuarios';
import Todoschats from './screens/Mensagem/Todoschats';
import Addfotoperfil from './screens/Editarperfil/Addfotoperfil';
import Agendausuario from './screens/Agendamentos/Agendausuario';
import Esqueceuasenhaemail from './screens/Recuperaraconta/Esqueceuasenhaemail';
import Esqueceuasenhacodigo from './screens/Recuperaraconta/Esqueceuasenhacodigo';
import Esqueceuasenhaescolher from './screens/Recuperaraconta/Esqueceuasenhaescolher';
import Esqueceuasenhaconfirmacao from './screens/Recuperaraconta/Esqueceuasenhaconfirmacao';
import Solicitacaoagendamento from './screens/Agendamentos/Solicitacaoagendamento';
import Outroususariosfotos from './screens/Profile/Outroususariosfotos';
import Mensagem from './screens/Mensagem/Mensagem';

const Stack = createNativeStackNavigator();

export default function App() {
  return (
    <NavigationContainer>
      <Stack.Navigator>

        <Stack.Screen name='SplashScreen' component={SplashScreen} options={{headerShown: false}}/>
        <Stack.Screen name='Login' component={Login} options={{headerShown: false}}/>
        <Stack.Screen name='Menu' component={Menu} options={{headerShown: false}}/>
        <Stack.Screen name='Addpost' component={Addpost} options={{headerShown: false}}/>
        <Stack.Screen name='Registraremail' component={Registraremail} options={{headerShown: false}}/>
        <Stack.Screen name='Registrarverificaremail' component={Registrarverificaremail} options={{headerShown: false}}/>
        <Stack.Screen name='Registrarusername' component={Registrarusername} options={{headerShown: false}}/>
        <Stack.Screen name='Registrarnovasenha' component={Registrarnovasenha} options={{headerShown: false}}/>
        <Stack.Screen name='Configuracao' component={Configuracao} options={{headerShown: false}}/>
        <Stack.Screen name='Mudarusername' component={Mudarusername} options={{headerShown: false}}/>
        <Stack.Screen name='Mudardescricao' component={Mudardescricao} options={{headerShown: false}}/>
        <Stack.Screen name='Mudarsenha' component={Mudarsenha} options={{headerShown: false}}/>
        <Stack.Screen name='Outrosusuarios' component={Outrosusuarios} options={{headerShown: false}}/>
        <Stack.Screen name='Todoschats' component={Todoschats} options={{headerShown: false}}/>
        <Stack.Screen name='Addfotoperfil' component={Addfotoperfil} options={{headerShown: false}}/>
        <Stack.Screen name='Agendausuario' component={Agendausuario} options={{headerShown: false}}/>
        <Stack.Screen name='Esqueceuasenhaemail' component={Esqueceuasenhaemail} options={{headerShown: false}}/>
        <Stack.Screen name='Esqueceuasenhacodigo' component={Esqueceuasenhacodigo} options={{headerShown: false}}/>
        <Stack.Screen name='Esqueceuasenhaescolher' component={Esqueceuasenhaescolher} options={{headerShown: false}}/>
        <Stack.Screen name='Esqueceuasenhaconfirmacao' component={Esqueceuasenhaconfirmacao} options={{headerShown: false}}/>
        <Stack.Screen name='Solicitacaoagendamento' component={Solicitacaoagendamento} options={{headerShown: false}}/>
        <Stack.Screen name='Outroususariosfotos' component={Outroususariosfotos} options={{headerShown: false}}/>
        <Stack.Screen name='Mensagem' component={Mensagem} options={{headerShown: false}}/>

      </Stack.Navigator>
    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
});
