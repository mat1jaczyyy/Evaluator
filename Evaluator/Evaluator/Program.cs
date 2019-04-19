using System;
using System.Diagnostics;
using System.IO;
using System.Threading;

namespace Evaluator
{
    class Program
    {
        public static string rootDir = @"C:\wamp\www\";
        public static string pyPath = @"C:\Python34\python.exe";
        public static StreamWriter log;

        public static void Main(string[] args)
        {
            int evaluated = -1;
            string[] programs = new string[0];

            while (true)
            {
                Thread.Sleep(500);

                try
                {
                    programs = File.ReadAllLines(rootDir + @"db\queue.db");
                }
                catch (IOException e)
                {
                    if (e.Message.Substring(0, "The process cannot access the file".Length) != "The process cannot access the file")
                    {
                        throw e;
                    }
                }

                /*
                if (evaluated == -1 && programs.Length > 15) {
                    Console.WriteLine("There are " + programs.Length.ToString() + " evaluations to be done. Do you want to skip (y/n)?");
                    if (Console.ReadKey().Key == ConsoleKey.Y) {
                        evaluated = programs.Length - 1;
                    }
                }
                */

                for (int k = evaluated + 1; k < programs.Length; k++)
                {
                    programs[k] = programs[k].Replace("/", @"\");
                    string[] path = programs[k].Split('\\');
                    string[] info = path[2].Split('-');

                    if (k > 0) Console.WriteLine("");
                    Console.WriteLine("Evaluation #" + k.ToString() + " {");
                    Console.WriteLine("    File: " + programs[k]);

                    if (File.Exists(rootDir + programs[k] + ".result"))
                    {
                        Console.WriteLine("    Already evaluated!");
                        Console.WriteLine("}");
                        evaluated++;
                    }
                    else
                    {

                        string verdict = "AC";

                        ProcessStartInfo pythonStartInfo = new ProcessStartInfo(pyPath);
                        pythonStartInfo.Arguments = "\"" + rootDir + programs[k] + "\"";
                        pythonStartInfo.UseShellExecute = false;
                        pythonStartInfo.RedirectStandardInput = true;
                        pythonStartInfo.RedirectStandardOutput = true;
                        pythonStartInfo.RedirectStandardError = true;

                        string[] dummy_files = Directory.GetFiles(rootDir + @"dummy_in\");
                        int dummy_tests = 0;
                        for (int i = 0; i < dummy_files.Length; i++)
                        {
                            if (dummy_files[i].Split('\\')[dummy_files[i].Split('\\').Length - 1].Split('.')[0] == info[0])
                            {
                                dummy_tests++;
                            }
                        }

                        string[] files = Directory.GetFiles(rootDir + @"in\");
                        int tests = 0;
                        for (int i = 0; i < files.Length; i++)
                        {
                            if (files[i].Split('\\')[files[i].Split('\\').Length - 1].Split('.')[0] == info[0])
                            {
                                tests++;
                            }
                        }

                        for (int i = 1; i <= dummy_tests; i++)
                        {
                            StreamReader testReader = new StreamReader(rootDir + @"dummy_in\" + info[0] + "." + i.ToString());
                            string testIn = testReader.ReadToEnd();
                            testReader.Close();

                            Process python = new Process();
                            python.StartInfo = pythonStartInfo;
                            python.Start();

                            python.StandardInput.Write(testIn);
                            python.StandardInput.Flush();

                            bool TLE = false;
                            while (true)
                            {
                                if (python.StartTime.AddSeconds(7) < DateTime.Now)
                                {
                                    if (!python.HasExited)
                                    {
                                        TLE = true;
                                        python.Kill();
                                    }
                                    break;
                                }
                                else if (python.HasExited)
                                {
                                    break;
                                }
                                else
                                {
                                    Thread.Sleep(500);
                                }
                            }

                            string testOut = python.StandardOutput.ReadToEnd().Replace("\r", "");
                            string expectedOut = File.ReadAllText(rootDir + @"dummy_out\" + info[0] + "." + i.ToString()).Replace("\r", "");

                            if (testOut != expectedOut)
                            {
                                verdict = "WA";
                            }

                            if (TLE)
                            {
                                verdict = "TLE";
                            }

                            string testErr = python.StandardError.ReadToEnd();

                            if (testErr != "")
                            {
                                verdict = "ERR";
                                string[] errorFile = programs[k].Split('\\');
                                StreamWriter errorWriter = new StreamWriter(rootDir + programs[k] + ".err");
                                errorWriter.Write(testErr);
                                errorWriter.Flush();
                                errorWriter.Close();
                            }

                            if (verdict != "AC")
                            {
                                Console.WriteLine("    Dummy test " + i.ToString() + ": FAILED");
                                string resultOut = "d\n" + (i - 1).ToString() + "\n" + verdict;

                                StreamWriter resultWriter = new StreamWriter(rootDir + programs[k] + ".result");
                                resultWriter.Write(resultOut);
                                resultWriter.Flush();
                                resultWriter.Close();

                                break;
                            }
                            else
                            {
                                Console.WriteLine("    Dummy test " + i.ToString() + ": OK");
                            }
                        }

                        if (verdict == "AC")
                        {
                            for (int i = 1; i <= tests; i++)
                            {
                                StreamReader testReader = new StreamReader(rootDir + @"in\" + info[0] + "." + i.ToString());
                                string testIn = testReader.ReadToEnd();
                                testReader.Close();

                                Process python = new Process();
                                python.StartInfo = pythonStartInfo;
                                python.Start();

                                python.StandardInput.Write(testIn);
                                python.StandardInput.Flush();

                                bool TLE = false;
                                while (true)
                                {
                                    if (python.StartTime.AddSeconds(7) < DateTime.Now)
                                    {
                                        if (!python.HasExited)
                                        {
                                            TLE = true;
                                            python.Kill();
                                        }
                                        break;
                                    }
                                    else if (python.HasExited)
                                    {
                                        break;
                                    }
                                    else
                                    {
                                        Thread.Sleep(500);
                                    }
                                }

                                string testOut = python.StandardOutput.ReadToEnd().Replace("\r", "");
                                string expectedOut = File.ReadAllText(rootDir + @"out\" + info[0] + "." + i.ToString()).Replace("\r", "");


                                if (testOut != expectedOut)
                                {
                                    verdict = "WA";
                                }

                                if (TLE)
                                {
                                    verdict = "TLE";
                                }

                                string testErr = python.StandardError.ReadToEnd();

                                if (testErr != "")
                                {
                                    verdict = "ERR";
                                    string[] errorFile = programs[k].Split('\\');
                                    StreamWriter errorWriter = new StreamWriter(rootDir + programs[k] + ".err");
                                    errorWriter.Write(testErr);
                                    errorWriter.Flush();
                                    errorWriter.Close();
                                }

                                if (verdict != "AC")
                                {
                                    Console.WriteLine("    Test " + i.ToString() + ": FAILED");
                                    string resultOut = "t\n" + (i - 1).ToString() + "\n" + verdict;

                                    StreamWriter resultWriter = new StreamWriter(rootDir + programs[k] + ".result");
                                    resultWriter.Write(resultOut);
                                    resultWriter.Flush();
                                    resultWriter.Close();

                                    break;
                                }
                                else
                                {
                                    Console.WriteLine("    Test " + i.ToString() + ": OK");
                                }
                            }
                        }

                        if (verdict == "AC")
                        {
                            string resultOut = "t\n" + tests.ToString() + "\n" + verdict;

                            StreamWriter resultWriter = new StreamWriter(rootDir + programs[k] + ".result");
                            resultWriter.Write(resultOut);
                            resultWriter.Flush();
                            resultWriter.Close();
                        }

                        Console.WriteLine("    Verdict: " + verdict);
                        Console.WriteLine("}");
                        evaluated++;
                    }
                }
            }
        }
    }
}